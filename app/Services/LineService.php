<?php

namespace App\Services;

use App\Models\Order;
use GuzzleHttp\Client;
use Jenssegers\Agent\Agent;

class LineService
{
    public function getLoginUrl()
    {
        // 組成 Line Login Url
        $url = config('services.line.authorize_base_url');
        $url .= '?response_type=code';
        $url .= '&client_id=' . config('services.line.channel_id');
        $url .= '&redirect_uri=' . config('app.url') . '/auth/line/callback';
        $url .= '&state='.csrf_token();
        $url .= '&scope=profile%20openid%20email';

        return $url;
    }

    public function getLineToken($code)
    {
        $client = new Client();
        $response = $client->request('POST', config('services.line.get_token_url'), [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('app.url') . '/auth/line/callback',
                'client_id' => config('services.line.channel_id'),
                'client_secret' => config('services.line.channel_secret')
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUserProfile($token)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        $response = $client->request('GET', config('services.line.get_user_profile_url'), [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getLinePayLink($order)
    {

        $line_url = config('line.LINE_PAY_URL');//line api
        $channel_ID = config('line.LINE_PAY_CHANNEL_ID');//your line sandbox ID
        $channel_serect = config('line.LINE_PAY_CHANNEL_SECRET');//your line sandbox serect
        $R_URI = '/v3/payments/request';//Request API URI
        $nonce = $this->generateUuid();//PHP uuid v4


        /*$products = array();
        $amount = 0;
        foreach($order->orderItems as $index=>$orderItem) {
            $price = intval($orderItem->sku_discount_price);
            $quantity = $orderItem->quantity;
            array_push($products, [
                "id" => strval($orderItem->sku_id),
                "name" => $orderItem->name,
                "quantity" => $quantity,
                "price" => $price
            ]);
            $amount = $amount + ($price*$quantity);
        }

        if($order->delivery_fee !== null) {
            $fee = intval($order->delivery_fee);
            array_push($products, [
                "id" => "99999",
                "name" => "運費",
                "quantity" => 1,
                "price" => $fee
            ]);
            $amount = $amount + $fee;
        }

        $packages = [[
            "id" => "0",
            "amount" => $amount,
            "products" => $products,
        ]];*/

        $packages = [[
            "id" => "0",
            "amount" => intval($order->total),
            "products" => [[
                "id" => "0",
                "name" => "求真草本購物",
                "quantity" => 1,
                "price" => intval($order->total)
            ]]
        ]];

        $r_body = json_encode(array(
            "amount" => intval($order->total),
            "currency" => "TWD",
            "orderId" => $order->id,
            "packages" => $packages,
            "redirectUrls" => array(
                'confirmUrl' => route('shop.pay.line.receive'),
                'cancelUrl' => route('account.orders.show', $order)
            ),
            "options" => array(
                "display"=>array(
                    "checkConfirmUrlBrowser" => false
                )
            )
        ));

        $Signature_data = $channel_serect . $R_URI . $r_body . $nonce;
        $Signature = base64_encode(hash_hmac('sha256', $Signature_data, $channel_serect,true));

        $_header = [
            'Content-Type'=>'application/json',
            'X-LINE-ChannelId'=>$channel_ID,
            'X-LINE-Authorization-Nonce'=>$nonce,
            'X-LINE-Authorization'=>$Signature
        ];

        $client = new Client();
        $response = $client->request('POST', $line_url.$R_URI, [
            'headers' => $_header,
            'body' => $r_body
        ]);
        $response =  json_decode($response->getBody()->getContents(), true);

        $agent = new Agent();
        if($agent->isPhone()) {
            return $response['info']['paymentUrl']['app'];
        } else {
            return $response['info']['paymentUrl']['web'];
        }

    }

    public function confirmPayment($request)
    {
        $order = Order::find($request->orderId);
        $order->update(['line_pay_transactionId'=>$request->transactionId]);

        $line_url = config('line.LINE_PAY_URL');//line api
        $channel_ID = config('line.LINE_PAY_CHANNEL_ID');//your line sandbox ID
        $channel_serect = config('line.LINE_PAY_CHANNEL_SECRET');//your line sandbox serect
        $R_URI = '/v3/payments/'.$request->transactionId.'/confirm';
        $nonce = $this->generateUuid();//PHP uuid v4

        $r_body = json_encode(
            [
                'amount'=>$order->total,
                'currency'=>'TWD'
            ]
        );

        $Signature_data = $channel_serect . $R_URI . $r_body . $nonce;
        $Signature = base64_encode(hash_hmac('sha256', $Signature_data, $channel_serect,true));

        $_header = [
            'Content-Type'=>'application/json',
            'X-LINE-ChannelId'=>$channel_ID,
            'X-LINE-Authorization-Nonce'=>$nonce,
            'X-LINE-Authorization'=>$Signature
        ];

        $client = new Client();
        $response = $client->request('POST', $line_url.$R_URI, [
            'headers' => $_header,
            'body' => $r_body
        ]);
        $response =  json_decode($response->getBody()->getContents(), true);

        if($response['returnCode'] === '0000') {
            return true;
        } else {
            return false;
        }
    }

    public function refund($request, $orderId)
    {
        $order = Order::find($orderId);
        $transactionId = $order->line_pay_transactionId;
        $line_url = config('line.LINE_PAY_URL');//line api
        $channel_ID = config('line.LINE_PAY_CHANNEL_ID');//your line sandbox ID
        $channel_serect = config('line.LINE_PAY_CHANNEL_SECRET');//your line sandbox serect
        $R_URI = '/v3/payments/'.$transactionId.'/refund';
        $nonce = $this->generateUuid();//PHP uuid v4

        $r_body = json_encode(
            [
                'refundAmount'=>$request->refundAmount,
            ]
        );

        $Signature_data = $channel_serect . $R_URI . $r_body . $nonce;
        $Signature = base64_encode(hash_hmac('sha256', $Signature_data, $channel_serect,true));

        $_header = [
            'Content-Type'=>'application/json',
            'X-LINE-ChannelId'=>$channel_ID,
            'X-LINE-Authorization-Nonce'=>$nonce,
            'X-LINE-Authorization'=>$Signature
        ];

        $client = new Client();
        $response = $client->request('POST', $line_url.$R_URI, [
            'headers' => $_header,
            'body' => $r_body
        ]);
        $response =  json_decode($response->getBody()->getContents(), true);

        if($response['returnCode'] === '0000') {
            return true;
        } else {
            return false;
        }
    }

    private function generateUuid()
    {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12);
        return $uuid;
    }
}
