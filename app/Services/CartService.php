<?php

namespace App\Services;

use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartService extends CartRepository
{
    public function guestCartToAuthCart()
    {
        if (isset($_COOKIE['skuIds'])) {
            $skuIds = explode(',', $_COOKIE['skuIds']);
            $quantities = explode(',', $_COOKIE['quantities']);
            setcookie("skuIds", "", time() - 3600);
            setcookie("quantities", "", time() - 3600);

            $user = User::find(Auth::user()->id);

            $cartItems = $user->carts;

            foreach($skuIds as $i=>$skuId) {
                if($cartItems->where('sku_id', $skuId)->count() !== 0)
                {
                    $user->carts()->where('sku_id', $skuId)->update(['quantity'=>$quantities[$i]]);
                } else {
                    $input['user_id'] = Auth::user()->id;
                    $input['sku_id'] = $skuId;
                    $input['quantity'] = $quantities[$i];
                    CartRepository::create($input);
                }
            }
        }
    }

    public function storeCart($request)
    {
        $userId = Auth::user()->id;
        $skuId = $request->skuId;
        $quantity = $request->quantity;
        $cart = Cart::where('user_id', $userId)->where('sku_id', $skuId)->first();
        $cartQuantity = count(Auth::user()->carts);
        if($cart === null) {
            $input = [
                'user_id'=> $userId,
                'sku_id'=>$skuId,
                'quantity'=>$quantity
            ];
            CartRepository::create($input);
            $cartQuantity = $cartQuantity+1;
        } else {
            $cart->update(['quantity'=>$quantity]);
        }
        return $cartQuantity;
    }

    public function updateCart($request)
    {
        $userId = Auth::user()->id;
        $skuId = $request->skuId;
        $quantity = $request->quantity;
        $cart = Cart::where('user_id', $userId)->where('sku_id', $skuId)->first();
        $cart->update(['quantity'=>$quantity]);
    }

    public function deleteCart($sku)
    {
        $cartId = DB::table('carts')->where('user_id', Auth::user()->id)->where('sku_id', $sku->id)->value('id');
        CartRepository::delete($cartId);
    }
}
