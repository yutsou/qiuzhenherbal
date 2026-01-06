<?php

namespace App\Http\Controllers;

use App\CustomFacades\CustomClass;
use App\Jobs\SendEmail;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\CouponService;
use App\Services\EcPayService;
use App\Services\InviteCodeService;
use App\Services\LineService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    private $productService;
    private $ecPayService;
    private $orderService;
    private $inviteCodeService;
    private $lineService;
    private $cartService;
    private $categoryService;
    private $couponService;
    private $userService;

    public function __construct(ProductService $productService, EcPayService $ecPayService, OrderService $orderService, InviteCodeService $inviteCodeService, LineService $lineService, CartService $cartService, CategoryService $categoryService, CouponService $couponService, UserService $userService)
    {
        $this->productService = $productService;
        $this->ecPayService = $ecPayService;
        $this->orderService = $orderService;
        $this->inviteCodeService = $inviteCodeService;
        $this->lineService = $lineService;
        $this->cartService = $cartService;
        $this->categoryService = $categoryService;
        $this->couponService = $couponService;
        $this->userService = $userService;
    }

    public function showHomePage()
    {
        $saleCategory = $this->categoryService->getCategory(10);
        $hotCategory = $this->categoryService->getCategory(16);
        $newCategory = $this->categoryService->getCategory(15);
        $description = '求真草本，以「草本養膚」為主，研發綠色、環保、護生、互助、互利、共生的產品，堅持選用有機檢驗認證原料，生產過程使用GMP品質系統監視與管理，全產品均在台灣生產製造，確保生產過程及品質，期盼讓更多人得到幫助，進而達成我們的品牌形象、信念及使命。';
        return view('home_page')->with('hotCategory', $hotCategory)->with('newCategory', $newCategory)->with('saleCategory', $saleCategory)->with('description', $description);
    }

    public function indexProducts()
    {
        $products = $this->productService->getAllProducts()->where('visible', 1);
        return CustomClass::viewWithTitle(view('shop.products.index')->with('products', $products), '所有商品');
    }

    public function showProduct($productId)
    {
        $product = $this->productService->getProduct($productId);
        return CustomClass::viewWithTitle(view('shop.products.show')->with('product', $product), $product->name);
    }

    public function showCart(Request $request)
    {
        if (Auth::check()) {
            if(Auth::user()->oauth_type === null) {
                if(Auth::user()->email_verified_at === null) {
                    return redirect()->route('account.email_verification.request');
                }
            }
            $results = $this->productService->getCartItems('session');
        } else {
            $results = $this->productService->getCartItems('cookie', $request);
        }

        if ($results !== null) {
            return CustomClass::viewWithTitle(view('shop.cart.show')->with('cartItems', $results[0])->with('cartSubtotal', $results[1])->with('cartExisted', true), '購物車');
        } else {
            return CustomClass::viewWithTitle(view('shop.cart.show')->with('cartExisted', false), '購物車');
        }
    }

    public function showDeliveryFeeCalculate(Request $request)
    {
        if (Auth::check()) {
            $results = $this->productService->getCartItems('session');
            if ($results === null){#判斷使用者使用上一頁操作的失誤
                return redirect()->route('shop.cart.show');
            }
        } else {
            $results = $this->productService->getCartItems('cookie', $request);
            if ($results === null){#判斷使用者使用上一頁操作的失誤
                return redirect()->route('shop.cart.show');
            }
        }

        return CustomClass::viewWithTitle(view('shop.delivery_fee.calculate')->with('cartSubtotal', $results[1]), '運費計算');
    }

    public function ajaxValidDeliveryDetailed(Request $request)
    {
        $input = $request->all();

        $rules = [
            'logisticsType'=>'required',
            'paymentMethod'=>'required',
            'receiverName'=>'required|min:2|max:10',
            'receiverCellPhone'=>'required|numeric|regex:/(09)[0-9]{8}/',
            'receiverEmail'=>'required'
        ];

        $messages = [
            'logisticsType.required'=>'請選擇運送方式',
            'paymentMethod.required'=>'請選擇付款方式',
            'receiverName.required'=>'請填寫收件人姓名',
            'receiverName.min'=>'收件人姓名需要大於 :min 個字元',
            'receiverName.max'=>'收件人姓名需要小於 :max 個字元',
            'receiverCellPhone.required'=>'請填寫收件人行動電話',
            'receiverCellPhone.regex'=>'手機格式錯誤',
            'receiverEmail.required'=>'請填寫收件人電子郵件'
        ];

        if($request->logisticsType == 'CVS') {
            $rules['logisticsSubType'] = 'required';
            $messages['logisticsSubType.required'] = '請選擇超商';
        } elseif($request->logisticsType == 'home-delivery') {
            $rules['county'] = 'required';
            $rules['district'] = 'required';
            $rules['zipcode'] = 'required';
            $rules['address'] = 'required';
            $messages['county.required'] = '請選擇您的縣市';
            $messages['district.required'] = '請選擇鄉鎮';
            $messages['zipcode.required'] = '請填寫郵遞區號';
            $messages['address.required'] = '請填寫地址';
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        } else {
            return response()->json(['success'=>'success']);
        }
    }

    public function submitDeliveryDetailed(Request $request)
    {
        if ($request->logisticsType === 'CVS') {
            $tmp_merchant_trade_no = 'MQH' . time();
            Cache::put($tmp_merchant_trade_no, $request->all(), now()->addDay());
            $this->ecPayService->selectReceiveStore($tmp_merchant_trade_no, $request->logisticsSubType);
        } else {
            return redirect()->route('shop.checkout.show')->with('deliveryDetailed', $request->all());
        }
    }

    public function showCheckout()
    {
        if(session('deliveryDetailed') === null) {
            return redirect()->route('shop.cart.show');
        }

        if (Auth::check()) {
            $results = $this->productService->getCartItems('session');
            if ($results === null){#判斷使用者使用上一頁操作的失誤
                return redirect()->route('shop.cart.show');
            }
        } else {
            $results = $this->productService->getCartItems('cookie');
            if ($results === null){#判斷使用者使用上一頁操作的失誤
                return redirect()->route('shop.cart.show');
            }
        }

        $deliveryDetailed = session('deliveryDetailed');

        return CustomClass::viewWithTitle(view('shop.checkout.show')->with('cartItems', $results[0])->with('cartSubtotal', $results[1])->with('deliveryDetailed', $deliveryDetailed), '結帳');
    }

    public function checkInventory($sku, $quantity)
    {
        return $this->productService->checkInventory($sku, $quantity);
    }

    public function ajaxFlashCart(Request $request)
    {
        $skuId = $request->skuId;
        $quantity = $request->quantity;
        $sku = $this->productService->getSKu($skuId);
        $discountPrice = $this->productService->getSkuDiscountPrice($sku, $quantity);
        return $discountPrice;
    }

    public function ajaxValidInventory(Request $request)
    {
        $input = $request->all();

        foreach($input['skuIds'] as $index=>$skuId) {
            $sku = $this->productService->getSKu($skuId);
            $checkResult = $this->checkInventory($sku, $input['quantities'][$index]);
            if($checkResult !== true) {
                if($sku->inventory_status === null) {
                    $message = $sku->product->name.' - 數量須小於等於'.$sku->inventory;
                } else {
                    $message = $sku->product->name.' - 等待補貨';
                }
                return response()->json(['error'=>[$message]]);
            } else {
                return response()->json(['success'=>'success']);
            }
        }
    }

    public function ajaxStoreCart(Request $request)
    {
        $sku = $this->productService->getSKu($request->skuId);
        $inventoryResult = $this->checkInventory($sku, $request->quantity);
        if ($inventoryResult) {
            $cartQuantity = $this->cartService->storeCart($request);
            return [true, $cartQuantity];
        } else {
            return [false];
        }
    }

    public function ajaxStoreCookieCart(Request $request)
    {
        $sku = $this->productService->getSKu($request->skuId);
        $inventoryResult = $this->checkInventory($sku, $request->quantity);
        if ($inventoryResult) {
            return [true];
        } else {
            return [false];
        }
    }

    public function ajaxUpdateCart(Request $request)
    {
        $this->cartService->updateCart($request);
        $discountPrice = $this->ajaxFlashCart($request);
        return $discountPrice;
    }

    public function ajaxGetInviteCodeDiscount($inviteCode)
    {
        return $this->inviteCodeService->getDiscount($inviteCode);
    }

    public function checkout(Request $request)
    {
        $this->productService->flashInventory($request);

        $order = $this->orderService->storeOrder($request);

        if($order->logistics_type === "CVS") {
            return ['3', route('echoT', $order)];
        } else {
            if($request->paymentMethod === 'COD' || $request->paymentMethod === 'noPayment') {
                if(isset($order->receiver_email)) {
                    $emailContent['type'] = 'receiveOrderNotification';
                    $emailContent['emailAddress'] = $order->receiver_email;
                    $emailContent['order'] = $order;
                    SendEmail::dispatch($emailContent);
                }
                $this->useCouponAndPointAndInviteCode($order);
                #return redirect(route('account.orders.show', $order))->with('Success', '訂單建立成功');
                return ['1', route('account.orders.show', $order)];
            } else {
                if($request->paymentMethod == 'linePay') {
                    return ['2', route('shop.pay', $order), route('shop.ajax.get.payment_status', $order), route('account.orders.show', $order)];
                } else {
                    #return redirect(route('shop.pay', $order));
                    return ['0', route('shop.pay', $order)];
                }
            }
        }

    }

    public function echoT($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        $this->ecPayService->createLogisticsOrder($order);
    }

    private function useCouponAndPointAndInviteCode($order)
    {
        if(Auth::check()) {
            if($order->coupon_id !== null) {
                $this->couponService->useCoupon($order->coupon_id);
            }
            if($order->point_discount !== null) {
                $this->userService->usePoint($order->point_discount);
            }
        }
        if($order->invite_code !== null) {
            $this->inviteCodeService->useInviteCode($order->invite_code, $order->total);
        }
    }

    public function mapEcpayServerReply(Request $request)
    {
        echo CustomClass::viewWithTitle(view('shop.checkout.loading'), '等待');

        $checkoutInfo = Cache::get($request->MerchantTradeNo);
        $checkoutInfo = array_merge([
            "CVSStoreID" => $request->CVSStoreID,
            "CVSStoreName" => $request->CVSStoreName,
            "CVSAddress" => $request->CVSAddress,
        ], $checkoutInfo);

        return redirect()->route('shop.checkout.show')->with('deliveryDetailed', $checkoutInfo);
        #$order = $this->orderService->updateOrderMap($request);
        #$this->ecPayService->createLogisticsOrder($order);
    }

    public function cvsOrderEcpayServerReply(Request $request)
    {
        if($this->ecPayService->checkMacValue($request, 'md5')){
            $order = $this->orderService->updateOrderCvsInfo($request);
            if(isset($order->receiver_email)) {
                $emailContent['type'] = 'receiveOrderNotification';
                $emailContent['emailAddress'] = $order->receiver_email;
                $emailContent['order'] = $order;
                SendEmail::dispatch($emailContent);
            }
            return '1|OK';
        }
    }

    public function cvsOrderEcpayClientReply(Request $request)
    {
        $orderId = $this->orderService->getOrderIdByMerchantTradeNo($request->MerchantTradeNo, 0);
        $order = $this->orderService->getOrder($orderId);
        if($order->payment_method == "CVSPay" || $order->payment_method == "noPayment") {
            return redirect(route('account.orders.show', $order))->with('Success', '訂單建立成功');
        } else {
            return redirect(route('shop.pay', $order));
        }
    }

    public function cvsOrderEcpayIssueReply($orderId)
    {
        return redirect(route('account.orders.show', ['orderId'=>$orderId]))->with('Error', '選擇的超商目前無法收貨，請選擇其他超商');
    }

    public function pay($orderId)
    {
        $order = $this->orderService->getOrder($orderId);

        if ($order->payment_method == "creditCard") {
            $this->ecPayService->creditCardPay($order);
        } elseif ($order->payment_method == "linePay") {
            return redirect($this->lineService->getLinePayLink($order));
        }
    }

    public function repay($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        if ($order->payment_method == "creditCard") {
            $this->orderService->rePayCreditCardUpdateMerchantTradeNo($order);
            $this->ecPayService->creditCardPay($order);
        } elseif ($order->payment_method == "linePay") {
            return redirect($this->lineService->getLinePayLink($order));
        }
    }

    public function payEcpayReceive(Request $request)
    {
        if($this->ecPayService->checkMacValue($request, 'sha256')){
            $orderId = $this->orderService->getOrderIdByMerchantTradeNo($request->MerchantTradeNo);
            $this->orderService->ecpayReceiveUpdateOrder($request, $orderId);
            if($request->RtnCode === '1') {
                $this->orderService->updatePaymentStatus(1, $orderId);
            }
            return '1|OK';
        }
    }

    public function payEcpayOrderReceive(Request $request)
    {
        $orderId = $this->orderService->getOrderIdByMerchantTradeNo($request->MerchantTradeNo);
        if($request->RtnCode === '1') {
            $order = $this->orderService->getOrder($orderId);
            $this->useCouponAndPointAndInviteCode($order);
            return redirect(route('account.orders.show', ['orderId'=>$orderId]))->with('Success', '付款完成');
        } else {
            return redirect(route('account.orders.show', $orderId))->with('Error', '付款失敗，請重新付款');
        }
    }

    public function payLineReceive(Request $request)
    {
        $result = $this->lineService->confirmPayment($request);
        if ($result) {
            $this->orderService->updateOrderLinePayInfo($request);
            $this->orderService->updatePaymentStatus(1, $request->orderId);
            $order = $this->orderService->getOrder($request->orderId);
            $this->useCouponAndPointAndInviteCode($order);

            return redirect(route('shop.pay.line.result', ['orderId'=>$request->orderId]))->with('Info', '等待付款結果');
        } else {
            return redirect(route('shop.pay.line.result', ['orderId'=>$request->orderId]))->with('Warning', '付款失敗，請重新付款');
        }
    }

    public function showPayLineResult($orderId)
    {
        return CustomClass::viewWithTitle(view('shop.linepay_result')->with('orderId', $orderId), '等待付款結果')->with('Info', '等待付款結果');
    }

    public function getOrderPaymentStatus($orderId)
    {
        $status = $this->orderService->getOrder($orderId)->payment_status;
        return $status;
    }

    public function deleteCart(Request $request, $skuId)
    {
        $sku = $this->productService->getSKu($request->skuId);
        $this->cartService->deleteCart($sku);
        return redirect()->back();
    }

    public function searchProducts(Request $request)
    {
        $productIds = $this->productService->searchSkuByName($request->keyword);
        $results = $this->productService->getProducts($productIds->pluck('id')->toArray());
        return CustomClass::viewWithTitle(view('shop.products.results')->with('results', $results), '搜尋結果');
    }

    public function indexCategoryProducts($categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $results = $category->products->where('visible', 1);
        return CustomClass::viewWithTitle(view('shop.products.results')->with('results', $results), $category->name);
    }

    public function showWarning()
    {
        return CustomClass::viewWithTitle(view('warning'), '警告');
    }
}
