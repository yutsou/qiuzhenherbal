<?php

namespace App\Services;

use App\Jobs\SendEmail;
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

    public function useCoupon($couponId)
    {
        $coupon = CouponRepository::find($couponId);
        $currentUsageCount = $coupon->usage_count;
        $currentUsageCount += 1;
        $coupon->update(['usage_count'=>$currentUsageCount]);
        Auth::user()->coupons()->detach($couponId);
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

    public function reductionCoupon($userId, $couponId)
    {
        $user = User::find($userId);
        $user->coupons()->attach($couponId);

        $coupon = CouponRepository::find($couponId);
        $currentUsageCount = $coupon->usage_count;
        $currentUsageCount -= 1;
        $coupon->update(['usage_count'=>$currentUsageCount]);
    }
}
