<?php

namespace App\Services;

use App\Models\Order;
use Ecpay\Sdk\Exceptions\RtnException;
use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Response\VerifiedArrayResponse;
use Illuminate\Support\Facades\Log;

class EcPayService
{
    public function selectReceiveStore($tmpMerchantTradeNo, $logisticsSubType)
    {
        try {
            $factory = new Factory([
                'hashKey' => config('ecpay.ECPAY_L_HASHKEY'),
                'hashIv' => config('ecpay.ECPAY_L_HASHIV'),
                'hashMethod' => 'md5',
            ]);
            $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

            $input = [
                'MerchantID' => config('ecpay.ECPAY_L_MERCHANTID'),
                'MerchantTradeNo' => $tmpMerchantTradeNo,
                'LogisticsType' => 'CVS',
                'LogisticsSubType' => $logisticsSubType,
                'ServerReplyURL' => route('shop.map.ecpay.server_reply'),
            ];
            $action = config('ecpay.ECPYA_LOGISTICS_URL').'/Express/map';

            echo $autoSubmitFormService->generate($input, $action);
        } catch (RtnException $e) {
            echo '(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL;
        }
    }

    public function createLogisticsOrder($order)
    {
        $lMerchantTradeNo = "LQH".time();
        $order->update(['l_merchant_trade_no'=>$lMerchantTradeNo]);
        try {
            $factory = new Factory([
                'hashKey' => config('ecpay.ECPAY_L_HASHKEY'),
                'hashIv' => config('ecpay.ECPAY_L_HASHIV'),
                'hashMethod' => 'md5',
            ]);
            $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

            $input = [
                'MerchantID' => config('ecpay.ECPAY_L_MERCHANTID'),
                'MerchantTradeNo' => $lMerchantTradeNo,
                'MerchantTradeDate' => date('Y/m/d H:i:s'),
                'LogisticsType' => 'CVS',
                'LogisticsSubType' => $order->logistics_sub_type,
                'GoodsAmount' => intval($order->total),
                'GoodsName' => '求真草本商品',
                'SenderName' => '鄧子宸',
                'SenderCellPhone' => '0937686926',
                'ReceiverName' => $order->receiver_name,
                'ReceiverCellPhone' => $order->receiver_cell_phone,
                'ServerReplyURL' => route('shop.cvs_order.ecpay.server_reply'),
                'ClientReplyURL' => route('shop.cvs_order.ecpay.client_reply'),
                'LogisticsC2CReplyURL' => route('shop.cvs_order.ecpay.issue_reply', ["orderId"=>$order->id]),
                'ReceiverStoreID' => $order->cvs_store_id
            ];

            if($order->payment_method == 'CVSPay'){
                $input['IsCollection'] = 'Y';
            } else {
                $input['IsCollection'] = 'N';
            }

            $action = config('ecpay.ECPYA_LOGISTICS_URL').'/Express/Create';

            echo $autoSubmitFormService->generate($input, $action);
        } catch (RtnException $e) {
            echo '(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL;
        }
    }

    public function checkMacValue($request, $hashMethod)
    {
        try {
            if($hashMethod === 'md5')
            {
                $factory = new Factory([
                    'hashKey' => config('ecpay.ECPAY_L_HASHKEY'),
                    'hashIv' => config('ecpay.ECPAY_L_HASHIV'),
                    'hashMethod' => $hashMethod,
                ]);
            } else {
                $factory = new Factory([
                    'hashKey' => config('ecpay.ECPAY_HASHKEY'),
                    'hashIv' => config('ecpay.ECPAY_HASHIV'),
                    'hashMethod' => $hashMethod,
                ]);
            }

            $checkoutResponse = $factory->create(VerifiedArrayResponse::class);

            Log::channel('ecpay')->info($checkoutResponse->get($request->toArray()));
            return true;
        } catch (RtnException $e) {
            Log::channel('ecpay')->warning('(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL);
            return false;
        }
    }

    public function creditCardPay($order)
    {
        try {
            $factory = new Factory([
                'hashKey' => config('ecpay.ECPAY_HASHKEY'),
                'hashIv' => config('ecpay.ECPAY_HASHIV'),
            ]);
            $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

            $itemNames = '';
            foreach($order->orderItems as $orderItem) {
                $itemNames = $itemNames.$orderItem->name.' NT$'.strval(number_format($orderItem->sku_discount_price)).' x'.strval($orderItem->quantity).'#';
            }

            $input = [
                'MerchantID' => config('ecpay.ECPAY_MERCHANTID'),
                'MerchantTradeNo' => $order->merchant_trade_no,
                'MerchantTradeDate' => date('Y/m/d H:i:s'),
                'PaymentType' => 'aio',
                'TotalAmount' => intval($order->total),
                'TradeDesc' => '求真草本購物',
                'ItemName' => $itemNames,
                'ReturnURL' => route('shop.pay.ecpay.receive'),
                'ClientBackURL' => route('account.orders.show', $order),
                'OrderResultURL'=> route('shop.pay.ecpay.order_receive'),
                'ChoosePayment' => 'Credit',
                'EncryptType' => 1,
            ];
            $action = config('ecpay.ECPYA_PAYMENT_URL').'/Cashier/AioCheckOut/V5';

            echo $autoSubmitFormService->generate($input, $action);
        } catch (RtnException $e) {
            echo '(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL;
        }
    }

    public function printCvsOrder($order)
    {
        try {
            $factory = new Factory([
                'hashKey' => config('ecpay.ECPAY_L_HASHKEY'),
                'hashIv' => config('ecpay.ECPAY_L_HASHIV'),
                'hashMethod' => 'md5',
            ]);
            $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

            $input = [
                'MerchantID' => config('ecpay.ECPAY_L_MERCHANTID'),
                'AllPayLogisticsID' => $order->all_pay_logistics_id,
                'CVSPaymentNo' => $order->cvs_payment_no,
            ];

            if($order->logistics_sub_type === "UNIMARTC2C") {
                $input['CVSValidationNo'] = $order->cvs_validation_no;
                $action = config('ecpay.ECPYA_LOGISTICS_URL').'/Express/PrintUniMartC2COrderInfo';
            } else {
                $action = config('ecpay.ECPYA_LOGISTICS_URL').'/Express/PrintFAMIC2COrderInfo';
            }

            echo $autoSubmitFormService->generate($input, $action);
        } catch (RtnException $e) {
            echo '(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL;
        }
    }
}
