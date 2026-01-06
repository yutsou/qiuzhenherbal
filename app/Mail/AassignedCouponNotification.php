<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AassignedCouponNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $emailContent;

    public function __construct($emailContent)
    {
        $this->emailContent = $emailContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.assigned_coupon_notification')
            ->with(
                [
                    'userName'=>$this->emailContent['userName'],
                    'couponName'=>$this->emailContent['couponName'],
                    'couponStartAt'=>$this->emailContent['couponStartAt'],
                    'couponEndAt'=>$this->emailContent['couponEndAt'],
                    'couponDiscountPrice'=>$this->emailContent['couponDiscountPrice'],
                ]
            )
            ->subject('優惠券已經發送到帳戶');
    }
}
