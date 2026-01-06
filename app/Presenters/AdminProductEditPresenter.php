<?php

namespace App\Presenters;

use Carbon\Carbon;

class AdminProductEditPresenter
{
    public function presentCheckedCategory($childrenCategory, $product)
    {
        $checkedCategoryIds = $product->categories->pluck('id')->toArray();
        $childrenCategoryId = $childrenCategory->id;

        if(in_array($childrenCategoryId, $checkedCategoryIds)) {
            return '<li class="uk-margin-left"><label><input class="uk-checkbox" type="checkbox" name="categories[]" value="'.$childrenCategory->id.'" checked> '.$childrenCategory->name.'</label></li>';
        } else {
            return '<li class="uk-margin-left"><label><input class="uk-checkbox" type="checkbox" name="categories[]" value="'.$childrenCategory->id.'"> '.$childrenCategory->name.'</label></li>';
        }
    }

    public function presentCheckedTag($tag, $product)
    {
        $checkedTagIds = $product->tags->pluck('id')->toArray();
        $tagId = $tag->id;

        if(in_array($tagId, $checkedTagIds)) {
            return '<li><label><input type="checkbox" class="uk-checkbox" name="tags[]" value="'.$tag->id.'" checked> '.$tag->name.'</label></li>';
        } else {
            return '<li><label><input type="checkbox" class="uk-checkbox" name="tags[]" value="'.$tag->id.'"> '.$tag->name.'</label></li>';
        }
    }

    public function timestampToDatetimeLocal($time)
    {
        if(isset($time)) {
            return Carbon::parse($time)->format('Y-m-d');
        } else {
            return null;
        }
    }
}
