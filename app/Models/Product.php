<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    public function otherImages()
    {
        return $this->hasMany(OtherImage::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function getMainCategoryAttribute()
    {
        return $this->categories->first();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function groupItems()
    {
        return $this->hasMany(GroupItem::class);
    }
}
