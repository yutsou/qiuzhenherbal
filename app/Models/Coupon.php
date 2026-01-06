<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getStartAtDateMinAttribute()
    {
        return $this->start_at->format('Y-m-d H:i');
    }

    public function getEndAtDateMinAttribute()
    {
        return$this->end_at->format('Y-m-d H:i');
    }
}
