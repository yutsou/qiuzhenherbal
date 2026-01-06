<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'ckfinder/*',
        '/map/ecpay/server-reply',
        '/cvs-order/ecpay/server-reply',
        '/cvs-order/ecpay/client-reply',
        '/cvs-order/ecpay/issue-reply',
        '/pay/ecpay/receive',
        '/pay/ecpay/order-receive',
        '/pay/line/receive',
        '/auth/facebook/callback'
    ];
}
