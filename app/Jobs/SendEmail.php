<?php

namespace App\Jobs;

use App\Mail\AassignedCouponNotification;
use App\Mail\EmailVerification;
use App\Mail\PasswordResetConfirmation;
use App\Mail\ReceiveOrderNotification;
use App\Mail\ShippedOrderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $emailContent;

    public function __construct($emailContent)
    {
        $this->emailContent = $emailContent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->emailContent['type'])
        {
            case 'emailVerification':
                $email = new EmailVerification($this->emailContent);
                Mail::to($this->emailContent['emailAddress'])->send($email);
                break;
            case 'receiveOrderNotification':
                $email = new ReceiveOrderNotification($this->emailContent);
                Mail::to($this->emailContent['emailAddress'])->send($email);
                break;
            case 'shippedOrderNotification':
                $email = new ShippedOrderNotification($this->emailContent);
                Mail::to($this->emailContent['emailAddress'])->send($email);
                break;
            case 'assignedCouponNotification':
                $email = new AassignedCouponNotification($this->emailContent);
                Mail::to($this->emailContent['emailAddress'])->send($email);
                break;
            case 'passwordResetConfirmation' :
                $email = new PasswordResetConfirmation($this->emailContent);
                Mail::to($this->emailContent['emailAddress'])->send($email);
                break;
        }
    }
}
