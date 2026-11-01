<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\Models\Coupon;
use App\Models\User;
use App\Repositories\CouponRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponService extends CouponRepository
{
    public function storeCoupon($request)
    {
        $endAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->end_at.' 23:59:59');

        $input = [
            'type'=>2,
            'name'=>$request->name,
            'discount_price'=>$request->discount_price,
            'start_at'=>$request->start_at,
            'end_at'=> $endAt->toDateTimeString(),
        ];
        return CouponRepository::create($input);
    }

    public function getAllCoupons()
    {
        return CouponRepository::all()->where('type', 2);
    }

    public function getCoupon($couponId)
    {
        return CouponRepository::find($couponId);
    }

    public function updateCoupon($request, $couponId)
    {
        $input = $request->all();
        CouponRepository::fill($input, $couponId);
    }

    public function assignCoupon($allUsers, $couponId)
    {
        $coupon = $this->getCoupon($couponId);

        foreach($allUsers as $user)
        {
            $user->coupons()->attach($couponId);

            if(isset($user->email)) {
                $emailContent['type'] = 'assignedCouponNotification';
                $emailContent['emailAddress'] = $user->email;
                $emailContent['userName'] = $user->name;
                $emailContent['couponName'] = $coupon->name;
                $emailContent['couponStartAt'] = $coupon->startAtDateMin;
                $emailContent['couponEndAt'] = $coupon->endAtDateMin;
                $emailContent['couponDiscountPrice'] = $coupon->discount_price;
                SendEmail::dispatch($emailContent);
            }
        }

        $coupon->update(['assigned'=>1]);
    }

    public function useCoupon($couponId, $user = null, $count = 1)
    {
        $user = $user ?? (Auth::check() ? Auth::user() : null);
        $coupon = CouponRepository::find($couponId);
        $count = max(1, intval($count));

        if ($user) {
            $matchingCoupons = $user->unusedUnexpiredCoupons->where('name', $coupon->name)->take($count);
            if ($matchingCoupons->count() > 0) {
                foreach ($matchingCoupons as $c) {
                    $c->update(['usage_count' => $c->usage_count + 1]);
                    DB::table('user_coupon')
                        ->where('user_id', $user->id)
                        ->where('coupon_id', $c->id)
                        ->where('used', 0)
                        ->limit(1)
                        ->update(['used' => 1]);
                    $user->coupons()->detach($c->id);
                }
                return;
            }
        }

        $currentUsageCount = $coupon->usage_count + $count;
        $coupon->update(['usage_count' => $currentUsageCount]);
        if ($user) {
            DB::table('user_coupon')
                ->where('user_id', $user->id)
                ->where('coupon_id', $couponId)
                ->where('used', 0)
                ->limit(1)
                ->update(['used' => 1]);
            $user->coupons()->detach($couponId);
        }
    }

    public function getAllCouponsByUser()
    {
        return User::find(Auth::user()->id)->unusedUnexpiredCoupons;
    }

    public function createAndAssignCouponToNewUser($user)
    {
        $startAt = Carbon::now()->startOfDay();
        $endAt = Carbon::now()->addMonth()->endOfDay();
        $input = [
            'type'=>0,
            'name'=>'新會員優惠券',
            'assigned'=>1,
            'discount_price'=>100,
            'start_at'=>$startAt->toDateTimeString(),
            'end_at'=> $endAt->toDateTimeString(),
        ];
        $newUserCoupon =  CouponRepository::create($input);

        $eventCouponIds = DB::table('coupons')->where('type', 2)->where('expired', 0)->where('assigned', 1)->pluck('id')->toArray();
        array_push($eventCouponIds, $newUserCoupon->id);
        $user->coupons()->attach($eventCouponIds);
    }

    public function assignBirthdayCoupons($userId)
    {
        $user = User::find($userId);
        $currentMonth = Carbon::now()->month;

        if($currentMonth == $user->birthday->month) {
            $eventCouponIds = DB::table('coupons')->where('type', 1)->where('expired', 0)->where('assigned', 1)->pluck('id')->toArray();
            $user->coupons()->attach($eventCouponIds);
        }
    }

    public function reductionCoupon($userId, $couponId, $count = 1)
    {
        $user = User::find($userId);
        $coupon = CouponRepository::find($couponId);
        $count = max(1, intval($count));

        if ($user) {
            for ($i = 0; $i < $count; $i++) {
                $user->coupons()->attach($couponId);
            }
        }

        $currentUsageCount = max(0, $coupon->usage_count - $count);
        $coupon->update(['usage_count' => $currentUsageCount]);
    }

    public function rewardDouble11Coupons($order)
    {
        if (!$order || !$order->user_id) {
            return;
        }

        if (!config('promotions.double_11.enabled', true)) {
            return;
        }

        // 僅在「訂單完成」狀態（delivery_status = 1）才發送折價券
        if ($order->delivery_status != 1) {
            return;
        }

        $startAtStr = config('promotions.double_11.start_at');
        $endAtStr = config('promotions.double_11.end_at');
        if (!$startAtStr || !$endAtStr) {
            return;
        }

        $startAt = Carbon::parse($startAtStr);
        $endAt = Carbon::parse($endAtStr);
        $orderCreatedAt = $order->created_at ? Carbon::parse($order->created_at) : Carbon::now();

        // 訂單建立時間必須在活動期間內
        if ($orderCreatedAt->lt($startAt) || $orderCreatedAt->gt($endAt)) {
            return;
        }

        // 防重複發放（冪等性）
        if (Coupon::where('order_id', $order->id)->where('type', 3)->exists()) {
            return;
        }

        // 按商品折扣後實付金額計算，不含運費
        $actualProductPaid = max(0, floatval($order->total) - floatval($order->delivery_fee));
        $threshold = config('promotions.double_11.reward_threshold', 1500);
        $giftCount = intval(floor($actualProductPaid / $threshold));

        if ($giftCount <= 0) {
            return;
        }

        $user = User::find($order->user_id);
        if (!$user) {
            return;
        }

        $couponName = config('promotions.double_11.coupon_name', '雙11滿千折百券');
        $discountPrice = config('promotions.double_11.reward_discount_price', 100);
        $minSpend = config('promotions.double_11.coupon_min_spend', 1000);
        $validDays = config('promotions.double_11.coupon_valid_days', 90);

        $couponStartAt = Carbon::now()->startOfDay();
        $couponEndAt = Carbon::now()->addDays($validDays)->endOfDay();

        for ($i = 0; $i < $giftCount; $i++) {
            $coupon = CouponRepository::create([
                'type' => 3,
                'name' => $couponName,
                'discount_price' => $discountPrice,
                'min_spend' => $minSpend,
                'is_accumulative' => true,
                'assigned' => 1,
                'usage_count' => 0,
                'expired' => 0,
                'order_id' => $order->id,
                'start_at' => $couponStartAt->toDateTimeString(),
                'end_at' => $couponEndAt->toDateTimeString(),
            ]);

            $user->coupons()->attach($coupon->id);
        }

        if (isset($user->email)) {
            $emailContent['type'] = 'assignedCouponNotification';
            $emailContent['emailAddress'] = $user->email;
            $emailContent['userName'] = $user->name;
            $emailContent['couponName'] = $giftCount > 1 ? "{$couponName} x{$giftCount}張" : $couponName;
            $emailContent['couponStartAt'] = $couponStartAt->format('Y-m-d H:i');
            $emailContent['couponEndAt'] = $couponEndAt->format('Y-m-d H:i');
            $emailContent['couponDiscountPrice'] = $discountPrice * $giftCount;
            SendEmail::dispatch($emailContent);
        }
    }

    public function revokeDouble11Coupons($orderId)
    {
        $coupons = Coupon::where('order_id', $orderId)->where('type', 3)->get();
        foreach ($coupons as $coupon) {
            if ($coupon->usage_count == 0) {
                $coupon->update(['expired' => 1]);
                DB::table('user_coupon')->where('coupon_id', $coupon->id)->delete();
            }
        }
    }
}
