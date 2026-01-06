<?php

namespace App\Jobs;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignBirthdayCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentMonth = Carbon::now()->month;
        $allUser = User::all();


        $startAt = Carbon::now()->startOfMonth();
        $endAt = Carbon::now()->endOfMonth();

        $input['type'] = 1;
        $input['name'] = $currentMonth.'月生日優惠券';
        $input['discount_price'] = 100;
        $input['start_at'] = $startAt;
        $input['end_at'] = $endAt;

        $coupon1 = $this->createBirthdayCoupon($input);
        $coupon2 = $this->createBirthdayCoupon($input);


        foreach($allUser as $user) {
            if(isset($user->birthday)){
                if($currentMonth == $user->birthday->month) {
                    $user->coupons()->attach($coupon1->id);
                    $user->coupons()->attach($coupon2->id);
                    if(isset($user->email)) {
                        $emailContent['type'] = 'assignedCouponNotification';
                        $emailContent['emailAddress'] = $user->email;
                        $emailContent['userName'] = $user->name;
                        $emailContent['couponName'] = $coupon1->name.'x2';
                        $emailContent['couponStartAt'] = $coupon1->start_at;
                        $emailContent['couponEndAt'] = $coupon1->end_at;
                        $emailContent['couponDiscountPrice'] = number_format($coupon1->discount_price);
                        SendEmail::dispatch($emailContent);
                    }
                }
            }
        }
        $coupon1->update(['assigned'=>1]);
        $coupon2->update(['assigned'=>1]);
    }

    public function createBirthdayCoupon($input)
    {
        return Coupon::create($input);
    }
}
