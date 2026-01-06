<?php

namespace App\Jobs;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckExpiredCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unExpiredCoupons = Coupon::where('expired', 0)->get();
        foreach($unExpiredCoupons as $unExpiredCoupon) {
            if(Carbon::now()->gt($unExpiredCoupon->end_at)){
                $unExpiredCoupon->update(['expired'=>1]);
            }
        }
    }
}
