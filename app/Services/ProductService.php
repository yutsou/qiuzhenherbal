<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\GroupItem;
use App\Models\OtherImage;
use App\Models\Product;
use App\Models\Sku;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class ProductService extends ProductRepository
{
    public function storeProduct($request)
    {
        $product = $this->createProduct($request);
        $sku = $this->createSku($request, $product);
        $this->saveManyOtherImages($request, $product);
        $this->saveManyDiscounts($request, $sku);
        $this->attachCategories($request, $product);
        $this->attachTags($request, $product);
        if ($request->type === 'group') {
            $this->saveManyGroupItems($request, $product);
        }
        return $product;
    }

    protected function createProduct($request)
    {
        $input = $this->getRequestProductInputs($request);
        return ProductRepository::create($input);
    }

    protected function getRequestProductInputs($request): array
    {
        $input['name'] = $request->name;
        $input['short_description'] = $request->short_description;
        $input['reminder'] = $request->reminder;
        $input['introduction'] = $request->introduction;
        $input['information'] = $request->information;
        $input['type'] = $request->type;
        $input['visible'] = $request->visible;
        $input['image_url'] = $request->mainImageUrl;
        $input['alt'] = $request->name;
        return $input;
    }

    protected function createSku($request, $product)
    {
        $input = $this->getRequestSkuInputs($request);
        $sku = $product->skus()->create($input);
        return $sku;
    }

    protected function getRequestSkuInputs($request): array
    {
        if($request->discount_start_at !== null) {
            $discountEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->discount_end_at.' 23:59:59');
            $input['discount_start_at'] = $request->discount_start_at;
            $input['discount_end_at'] = $discountEndAt->toDateTimeString();
        } else {
            $input['discount_start_at'] = null;
            $input['discount_end_at'] = null;
        }


        $input['number'] = $request->number;
        $input['regular_price'] = $request->regular_price;
        $input['inventory'] = $request->inventory;
        $input['inventory_status'] = $request->inventory_status;

        return $input;
    }

    protected function saveManyOtherImages($request, $product)
    {
        if (isset($request->otherImageUrls)) {
            $inputModels = array();
            foreach($request->otherImageUrls as $i=>$otherImageUrl) {
                $input['image_url'] = $otherImageUrl;
                $input['alt'] = $request->name.'-'.$i;

                array_push($inputModels, new OtherImage($input));
            }
            $product->otherImages()->saveMany($inputModels);
        }
    }

    protected function saveManyDiscounts($request, $sku)
    {
        if (isset($request->mins)) {
            $inputModels = array();
            foreach($request->mins as $i=>$min) {
                $input['min'] = $request->mins[$i];
                $input['max'] = $request->maxs[$i];
                $input['discount'] = $request->discount_price[$i];

                array_push($inputModels, new Discount($input));
            }
            $sku->discounts()->saveMany($inputModels);
        }
    }

    protected function saveManyGroupItems($request, $product)
    {
        if (isset($request->groupSkuIds)) {
            $inputModels = array();
            foreach($request->groupSkuIds as $i=>$groupSkuId) {
                $input['product_id'] = $product->id;
                $input['sku_id'] = $request->groupSkuIds[$i];
                $input['quantity'] = $request->groupItemQuantities[$i];

                array_push($inputModels, new GroupItem($input));
            }
            $product->groupItems()->saveMany($inputModels);
        }
    }

    protected function attachCategories($request, $product)
    {
        $product->categories()->attach($request->categories);
    }

    protected function attachTags($request, $product)
    {
        $product->tags()->attach($request->tags);
    }

    public function getProduct($productId)
    {
        $product = ProductRepository::find($productId);
        return $product;
    }

    public function getAllProducts()
    {
        $products = ProductRepository::all();
        return $products;
    }

    public function updateProduct($request, $productId)
    {
        $product = $this->getProduct($productId);
        $sku = $product->skus->first();
        $this->protectedUpdateProduct($request, $product);
        $this->updateSku($request, $sku);
        $this->updateManyOtherImages($request, $product);
        $this->updateManyDiscounts($request, $sku);
        $this->syncCategories($request, $product);
        $this->syncTags($request, $product);
        if ($request->type === 'group') {
            $this->updateManyGroupItems($request, $product);
        }
    }

    protected function protectedUpdateProduct($request, $product)
    {
        $input = $this->getRequestProductInputs($request);
        ProductRepository::update($input, $product->id);
    }

    protected function updateSku($request, $sku)
    {
        $input = $this->getRequestSkuInputs($request);
        $sku->update($input);
    }

    protected function updateManyOtherImages($request, $product)
    {
        $product->otherImages()->delete();
        $this->saveManyOtherImages($request, $product);
    }

    protected function updateManyDiscounts($request, $sku)
    {
        $sku->discounts()->delete();
        $this->saveManyDiscounts($request, $sku);
    }

    protected function syncCategories($request, $product)
    {
        $product->categories()->sync($request->categories);
    }

    protected function syncTags($request, $product)
    {
        $product->tags()->sync($request->tags);
    }

    protected function updateManyGroupItems($request, $product)
    {
        $product->groupItems()->delete();
        $this->saveManyGroupItems($request, $product);
    }

    public function ajaxGetProducts($visible)
    {
        if($visible === "1") {
            $products = ProductRepository::all()->where('visible', true);
        } else {
            $products = ProductRepository::all()->where('visible', false);
        }

        #$products = $this->getAllProducts();

        $datatable = DataTables::collection($products)
            ->addColumn('name', function ($product)
            {
                return '<a class="uk-link" href="'.route('admin.products.edit', $product).'">'.$product->name.'</a>';
            })
            ->addColumn('inventoryStatus', function ($product)
            {
                if ($product->type === 'general') {
                    $sku = $product->skus->first();
                    $inventoryStatus = $sku->inventory_status;
                    if ($inventoryStatus !== null) {
                        $statusTransformer = ['<span style="color:#f0506e;">售完</span>', '<span style="color:#32d296;">尚有庫存</span>', '<span style="color:#faa05a;">補貨中</span>'];
                        return $statusTransformer[$inventoryStatus];
                    } else {
                        $inventory = $sku->inventory;
                        if ($inventory !== 0) {
                            return '<span style="color:#32d296;">尚有庫存 ('.$inventory.')</span>';
                        } else {
                            return '<span style="color:#f0506e;">售完 (0)</span>';
                        }
                    }
                }
            })
            ->addColumn('regularPrice', function ($product)
            {
                $sku = $product->skus->first();
                $discount = $sku->discounts();
                if(isset($sku->discount_start_at)) {
                    $now = Carbon::now();
                    $carbonDiscountEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_end_at);
                    if($carbonDiscountEndAt->lte($now)) {
                        $timeTag = ' 優惠結束';
                    } else {
                        $timeTag = '';
                    }

                    if ($discount->count() === 0) {
                        return 'NT$'.number_format($sku->regular_price);
                    } elseif ($discount->count() === 1) {
                        return '<strike>'.'NT$'.number_format($sku->regular_price).'</strike> '.'NT$'.number_format($discount->first()->discount).$timeTag;
                    } else {
                        return '<strike>'.'NT$'.number_format($sku->regular_price).'</strike> '.'階段優惠'.$timeTag;
                    }
                } else {
                    if ($discount->count() === 0) {
                        return 'NT$'.number_format($sku->regular_price);
                    } elseif ($discount->count() === 1) {
                        return '<strike>'.'NT$'.number_format($sku->regular_price).'</strike> '.'NT$'.number_format($discount->first()->discount);
                    } else {
                        return '<strike>'.'NT$'.number_format($sku->regular_price).'</strike> '.'階段優惠';
                    }
                }

            })
            ->addColumn('type', function ($product)
            {
                $typeTransformedList = array('general'=>'一般', 'group'=>'群組');
                return $typeTransformedList[$product->type];
            })
            ->addColumn('categories', function ($product)
            {
                return $product->categories()->pluck('name')->implode(' ');
            })
            ->addColumn('tags', function ($product)
            {
                return $product->tags()->pluck('name')->implode(' ');
            })
            ->addColumn('checkbox', function ($product){
                return '<label><input class="uk-checkbox" type="checkbox" name="productIds[]" value="'.$product->id.'"></label>';
            })
            ->rawColumns(['name', 'inventoryStatus', 'regularPrice', 'type', 'categories', 'tags', 'checkbox'])
            ->toJson();

        return  $datatable;
    }
    public function searchSkuByName($keyword)
    {
        $results = DB::table('products')
            ->where('type', 'general')
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->select('products.name', 'skus.id')
            ->where('name', 'like', '%'.$keyword.'%')
            ->limit(5)
            ->get();
        return $results;
    }

    public function ajaxSearchSkuByName($keyword)
    {
        $results = $this->searchSkuByName($keyword);
        return Response::json( $results );
    }

    public function getInventory($skuId)
    {
        return DB::table('skus')->find($skuId)->inventory;
    }

    public function getSKu($skuId)
    {
        return Sku::find($skuId);
    }

    public function getSkuDiscountPrice($sku, $quantity)
    {
        $discounts = $sku->discounts;

        if(isset($sku->discount_start_at)) {
            $now = Carbon::now();
            $carbonDiscountStartAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_start_at);
            $carbonDiscountEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_end_at);
            if($carbonDiscountEndAt->gt($now) && $carbonDiscountStartAt->lte($now)) {
                if (count($discounts) === 0) {#防呆用，設置了時間但沒有優惠的情況
                    return $sku->regular_price;
                } elseif (count($discounts) === 1) {
                    if ($quantity >= $discounts->first()->min){
                        return $discounts->first()->discount;
                    } else {
                        return $sku->regular_price;
                    }
                } else {
                    if ($quantity > $discounts->last()->max)#如果數量大於最大數量，則用最大折扣
                    {
                        return $discounts->last()->discount;
                    } else {#判斷在哪個則扣區間
                        foreach ($discounts as $discount) {
                            if ($quantity >= $discount->min && $quantity <= $discount->max) {
                                return $discount->discount;
                            }
                        }
                        return $sku->regular_price;
                    }
                }
            } else {
                return $sku->regular_price;
            }
        } else {
            if (count($discounts) === 0) {
                return $sku->regular_price;
            } elseif (count($discounts) === 1) {
                if ($quantity >= $discounts->first()->min){
                    return $discounts->first()->discount;
                } else {
                    return $sku->regular_price;
                }
            } else {
                if ($quantity > $discounts->last()->max)#如果數量大於最大數量，則用最大折扣
                {
                    return $discounts->last()->discount;
                } else {#判斷在哪個折扣區間
                    foreach ($discounts as $discount) {
                        if ($quantity >= $discount->min && $quantity <= $discount->max) {
                            return $discount->discount;
                        }
                    }
                    return $sku->regular_price;
                }
            }
        }
    }

    private function removeCookieCartRemovedSku()
    {
        if (isset($_COOKIE['skuIds']) && isset($_COOKIE['quantities'])) {
            $skuIds = explode(',', $_COOKIE['skuIds']);
            $quantities = explode(',', $_COOKIE['quantities']);
            foreach($skuIds as $index=>$skuId) {
                $sku = $this->getSKu($skuId);
                if($sku === null || $sku->product->visible === 0){
                    array_splice($skuIds, $index, 1);
                    array_splice($quantities, $index, 1);
                }
            }
            $newSkuIdsString = implode(',', $skuIds);
            $newQuantitiesString = implode(',', $quantities);

            setcookie("skuIds", $newSkuIdsString);
            setcookie("quantities", $newQuantitiesString);
            $_COOKIE['skuIds'] = implode(',', $skuIds);
            $_COOKIE['quantities'] = implode(',', $quantities);

        }
    }

    public function getCartItems($sourceType, $request = null)
    {
        if($sourceType == 'cookie') {
            $this->removeCookieCartRemovedSku();
            if (isset($_COOKIE['skuIds']) && isset($_COOKIE['quantities'])) {
                if($_COOKIE['skuIds'] !== "") {#removeCookieCartRemovedSku沒有商品時
                    setcookie('skuIds', null, -1, '/delivery-fee');
                    setcookie('quantities', null, -1, '/delivery-fee');

                    $skuIds = explode(',', $_COOKIE['skuIds']);
                    $quantities = explode(',', $_COOKIE['quantities']);
                    return $this->cartItemsToCollection($skuIds, $quantities);
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            $cartItems = Auth::user()->carts;
            if($cartItems->count() !== 0) {
                $skuIds = $cartItems->pluck('sku_id');
                $quantities = $cartItems->pluck('quantity');
                return $this->cartItemsToCollection($skuIds, $quantities);
            } else {
                return null;
            }
        }
    }

    private function cartItemsToCollection($skuIds, $quantities)
    {
        $cartSubtotal = 0;
        $cartItems = collect();
        foreach($skuIds as $i=>$skuId) {
            $sku = $this->getSKu($skuId);
            if($sku !== null){
                $quantity = $quantities[$i];
                $discountPrice = $this->getSkuDiscountPrice($sku, $quantity);
                $itemSubtotal = intval($quantity)*intval($discountPrice);
                $product = $sku->product;
                $cartItem = [
                    'skuId'=>$skuId,
                    'imageUrl'=>$sku->product->image_url,
                    'productId'=>$product->id,
                    'name'=>$product->name,
                    'discountPrice'=>$discountPrice,
                    'quantity'=>$quantity,
                    'inventory'=>$sku->inventory,
                    'productSubtotal'=>$itemSubtotal
                ];
                $cartItems->push($cartItem);
                $cartSubtotal =  $cartSubtotal+$itemSubtotal;
            }
        }

        return [$cartItems, $cartSubtotal];
    }

    public function flashInventory($request)
    {
        foreach($request->itemNames as $i=>$iteName) {
            $sku = $this->getSKu($request->skuIds[$i]);
            if($sku->inventory !== null) {
                $currentInventory = $sku->inventory;
                $sku->update(['inventory'=>$currentInventory-$request->quantities[$i]]);
            }
        }
    }

    public function getProducts(array $productIds)
    {
        return Product::whereIn('id', $productIds)->where('visible', 1)->get();
    }

    public function deleteProducts($productIds)
    {
        foreach ($productIds as $productId) {
            $product = $this->getProduct($productId);
            $product->delete();
        }
    }

    public function checkInventory($sku, $quantity)
    {
        if($sku->inventory_status === 1) {
            return true;
        } elseif($sku->inventory_status === null) {
            if(intval($quantity) <= $sku->inventory) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
}
