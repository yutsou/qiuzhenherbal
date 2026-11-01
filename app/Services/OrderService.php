<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\User;
use App\Presenters\ShopPresenter;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderService extends OrderRepository
{
    public function storeOrder($request)
    {

        $input = [
            'subtotal' => $request->subtotal,
            'delivery_fee' => $request->deliveryFee,
            'invite_discount' => $request->inviteCodeDiscount,
            'coupon_discount' => $request->couponDiscount,
            'point_discount' => $request->pointDiscount,
            'total' => $request->total,
            'logistics_type' => $request->logisticsType,
            'payment_method' => $request->paymentMethod,
            'receiver_name' => $request->receiverName,
            'receiver_cell_phone' => $request->receiverCellPhone,
            'receiver_email' => $request->receiverEmail,
            'delivery_status' => 0,
            'invite_code' => $request->inviteCode,
            'coupon_id' => $request->coupon,
            'remark' => $request->remark,
        ];

        if (Auth::check()){
            $input['user_id'] = Auth::user()->id;

            Auth::user()->carts()->delete();

            if($request->coupon !== null) {
                $coupon = Coupon::find($request->coupon);
                if ($coupon) {
                    $couponCount = max(1, intval($request->couponCount ?? 1));

                    // 後端驗證門檻：若有設定門檻，需符合小計要求
                    if ($coupon->min_spend > 0) {
                        $requiredSubtotal = $coupon->is_accumulative ? ($coupon->min_spend * $couponCount) : $coupon->min_spend;
                        if ($request->subtotal < $requiredSubtotal) {
                            abort(400, '未達優惠券使用門檻');
                        }
                    }

                    // 驗證持有數量
                    $holdingCount = Auth::user()->unusedUnexpiredCoupons->where('name', $coupon->name)->count();
                    if ($holdingCount < $couponCount) {
                        abort(400, '持有的優惠券張數不足');
                    }

                    $expectedDiscount = $coupon->discount_price * $couponCount;
                    $input['coupon_id'] = $coupon->id;
                    $input['coupon_count'] = $couponCount;
                    $input['coupon_discount'] = $expectedDiscount;
                    $input['coupon_name'] = $couponCount > 1 ? ($coupon->name . ' x' . $couponCount) : $coupon->name;
                }
            } else {
                $input['coupon_id'] = null;
                $input['coupon_count'] = 1;
                $input['coupon_discount'] = null;
                $input['coupon_name'] = null;
            }
        } else {
            setcookie("skuIds", "", time() - 3600);
            setcookie("quantities", "", time() - 3600);
            $input['user_id'] = null;
            $input['coupon_id'] = null;
            $input['coupon_count'] = 1;
            $input['coupon_discount'] = null;
            $input['coupon_name'] = null;
        }

        if($request->paymentMethod === "creditCard") {
            $input['merchant_trade_no'] = 'QH' . time();
        }

        if($request->logisticsType === "home-delivery") {
            $input['county'] = $request->county;
            $input['district'] = $request->district;
            $input['zip_code'] = $request->zipcode;
            $input['address'] = $request->address;
            if($request->paymentMethod === "COD" || $request->paymentMethod === "noPayment") {
                $input['payment_status'] = 2;
            } else {
                $input['payment_status'] = 0;
            }
        } else {#CVS
            $input['cvs_store_id'] = $request->cvsStoreId;
            $input['cvs_store_name'] = $request->cvsStoreName;
            $input['cvs_address'] = $request->cvsAddress;

            $input['logistics_sub_type'] = $request->logisticsSubType;
            if($request->paymentMethod === "CVSPay" || $request->paymentMethod === "noPayment") {
                $input['payment_status'] = 2;
            } else {
                $input['payment_status'] = 0;
            }
        }

        $order = OrderRepository::create($input);

        $this->saveManyOrderItems($request, $order);

        return $order;
    }

    protected function saveManyOrderItems($request, $order)
    {
        $inputModels = array();
        foreach($request->itemNames as $i=>$iteName) {
            $input['name'] = $iteName;
            $input['sku_id'] = $request->skuIds[$i];
            $input['sku_discount_price'] = $request->discountPrices[$i];
            $input['quantity'] = $request->quantities[$i];
            $input['subtotal'] = $request->productSubtotals[$i];

            array_push($inputModels, new OrderItem($input));
        }
        $order->orderItems()->saveMany($inputModels);
    }

    public function getOrderIdByMerchantTradeNo($merchantTradeNo, $type = 1)
    {
        if($type == 1) {
            return DB::table('orders')->where('merchant_trade_no', $merchantTradeNo)->first()->id;
        } else {
            return DB::table('orders')->where('l_merchant_trade_no', $merchantTradeNo)->first()->id;
        }

    }

    public function getOrder($orderId)
    {
        return OrderRepository::find($orderId);
    }

    public function updateOrderCvsInfo($request)
    {
        $input = [
            'merchant_id' => $request->MerchantID,
            'rtn_code' => $request->RtnCode,
            'rtn_msg' => $request->RtnMsg,
            'all_pay_logistics_id' => $request->AllPayLogisticsID,
            'logistics_sub_type' => $request->LogisticsSubType,
            'cvs_payment_no' => $request->CVSPaymentNo,
            'cvs_validation_no' => $request->CVSValidationNo,
        ];

        $orderId = $this->getOrderIdByMerchantTradeNo($request->MerchantTradeNo, 0);
        OrderRepository::update($input, $orderId);
        return $this->getOrder($orderId);
    }

    public function updateOrderLinePayInfo($request)
    {
        $input = [
            'line_pay_transactionId' => $request->transactionId
        ];
        OrderRepository::update($input, $request->orderId);
    }

    public function updatePaymentStatus($status, $orderId)
    {
        $input['payment_status'] = $status;
        OrderRepository::update($input, $orderId);
    }

    public function ecpayReceiveUpdateOrder($request, $orderId)
    {
        $input['trade_no'] = $request->TradeNo;
        $input['rtn_code'] = $request->RtnCode;
        $input['rtn_msg'] = $request->RtnMsg;
        OrderRepository::update($input, $orderId);
    }

    public function getOrdersByAdmin()
    {
        $orders = OrderRepository::all();
        $notPaidOrders = $orders->where('delivery_status', 0)->where('payment_status', 0)->sortByDesc('created_at');
        $waitDeliverOrders = $orders->where('delivery_status', 0)->whereIn('payment_status', [1,2])->sortByDesc('created_at');
        $deliveredOrders = $orders->where('delivery_status', 2)->whereIn('payment_status', [1,2])->sortByDesc('created_at');

        return [$notPaidOrders, $waitDeliverOrders, $deliveredOrders];
    }

    public function ajaxGetAllOrders()
    {
        $orders = OrderRepository::all()->sortByDesc('created_at');

        $datatable = DataTables::collection($orders)
            ->addColumn('idName', function ($order)
            {
                return '#'.$order->id.' '.$order->receiver_name;
            })
            ->addColumn('created_at', function ($order)
            {
                return $order->created_at;
            })
            ->addColumn('status', function ($order){
                $shopPresenter = new ShopPresenter();
                return $shopPresenter->presentOrderStatus($order->delivery_status, $order->payment_status);
            })
            ->addColumn('total', function ($order)
            {
                return 'NT$'.number_format($order->total);
            })
            ->addColumn('action', function ($order)
            {
                return '<a class="uk-button custom-button-1" href="'.route('admin.orders.show', $order).'">查看</a>';
            })
            ->rawColumns(['idName', 'created_at', 'status', 'total', 'action'])
            ->toJson();

        return  $datatable;
    }

    public function updateOrder($orderId, $request)
    {
        $order = $this->getOrder($orderId);

        switch ($order->delivery_status) {
            case 0:
                if($order->payment_status !== 0) {
                    $input['delivery_status'] = 2;
                    if(isset($order->receiver_email)) {
                        $emailContent['type'] = 'shippedOrderNotification';
                        $emailContent['emailAddress'] = $order->receiver_email;
                        $emailContent['order'] = $order;
                        SendEmail::dispatch($emailContent);
                    }
                }
                break;
            case 1:
                $input['delivery_status'] = 3;
                break;
            case 2:
                $input['delivery_status'] = 1;
                break;
            case 3:
                $input['delivery_status'] = 4;
                break;
        }
        OrderRepository::update($input, $orderId);

        if (isset($input['delivery_status']) && $input['delivery_status'] == 1) {
            $completedOrder = $this->getOrder($orderId);
            app(CouponService::class)->rewardDouble11Coupons($completedOrder);
        }
    }

    public function cancelOrder($orderId)
    {
        $input['delivery_status'] = 4;
        $input['payment_status'] = 0;
        OrderRepository::update($input, $orderId);
    }

    public function getAllOrdersByUser()
    {
        return User::find(Auth::user()->id)->orders()->orderBy('created_at', 'desc')->paginate(20);
    }

    public function pointRefund($request, $orderId)
    {
        $order = $this->getOrder($orderId);
        $user = User::find($order->user_id);
        $ownPoint = intval($user->point);
        $refundPoint = intval($request->refundAmount);
        $user->update(['point'=>$ownPoint+$refundPoint]);
        $input['refund_remark']= '點數退款'.$refundPoint.'點';
        $order->update($input);
    }

    public function otherRefund($request, $orderId)
    {
        $order = $this->getOrder($orderId);
        $input['refund_remark']= $request->refund_remark;
        $order->update($input);
    }

    public function rePayCreditCardUpdateMerchantTradeNo($order)
    {
        $order->update(['merchant_trade_no' => 'QHre' . time()]);

    }
}
