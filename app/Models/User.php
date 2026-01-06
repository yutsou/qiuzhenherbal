<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'oauth_type',
        'oauth_id',
        'oauth_email',
        'role',
        'phone',
        'birthday',
        'county',
        'district',
        'zip_code',
        'address',
        'confirm_token',
        'email_verified_at',
        'point'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'datetime',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'user_coupon');
    }

    public function getUnusedUnexpiredCouponsAttribute()
    {
        return $this->coupons()->where('used', 0)->get()->where('expired', 0);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
