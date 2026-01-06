<?php

namespace App\Presenters;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShopPresenter
{
    public function presentPrice($product)
    {
        $sku = $product->skus->first();
        $discounts = $sku->discounts;
        $regularPrice = $sku->regular_price;
        if(isset($sku->discount_start_at)) {
            $now = Carbon::now();
            $carbonDiscountStartAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_start_at);
            $carbonDiscountEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_end_at);

            if($carbonDiscountEndAt->gt($now) && $carbonDiscountStartAt->lte($now)){
                $html = '<span style="color: #ee782e;">優惠開始時間: '.$carbonDiscountStartAt->format('Y-m-d H:i').'<br>優惠結束時間: '.$carbonDiscountEndAt->format('Y-m-d H:i').'</span><br><br>';

                if (count($discounts) === 0) {#防呆用，設置了時間但沒有優惠的情況
                    return '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice).'</span>';
                } elseif (count($discounts) === 1) {
                    $html .= '<span style="color: #ee782e; font-size: 1.5em;"><br><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike> NT$' . number_format($discounts->first()->discount).'</span>';
                    return $html;
                } else {
                    if ($discounts->first()->min === 1) {
                        $html .= '<span style="color: #ee782e; font-size: 1.5em;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</span>';
                        return $html;
                    } else {
                        $html .= '<span style="color: #ee782e; font-size: 1.5em;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</span>';
                        return $html;
                    }
                }
            } else {
                if($carbonDiscountEndAt->lte($now)) {
                    $html = '<span style="color: #888;"><strike>優惠開始時間: '.$carbonDiscountStartAt->format('Y-m-d H:i').'<br>優惠結束時間: '.$carbonDiscountEndAt->format('Y-m-d H:i').'</strike></span><br><br>';
                } else {
                    $html = '<span style="color: #ee782e;">優惠開始時間: '.$carbonDiscountStartAt->format('Y-m-d H:i').'<br>優惠結束時間: '.$carbonDiscountEndAt->format('Y-m-d H:i').'</span><br><br>';
                }

                if (count($discounts) === 0) {#防呆用，設置了時間但沒有優惠的情況
                    return '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice).'</span>';
                } elseif (count($discounts) === 1) {
                    $html .= '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice) . ' <strike style="color: #888;">NT$' . number_format($discounts->first()->discount).'</strike></span>';
                    return $html;
                } else {
                    if ($discounts->first()->min === 1) {
                        $html .= '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice) . '<br><strike style="color: #888;">NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</strike></span>';
                        return $html;
                    } else {
                        $html .= '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice) . '<br><strike style="color: #888;">NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</strike></span>';
                        return $html;
                    }
                }
            }
        } else {
            if (count($discounts) === 0) {
                return '<span style="color: #333; font-size: 1.5em;">NT$' . number_format($regularPrice).'</span>';
            } elseif (count($discounts) === 1) {
                return '<span style="color: #ee782e; font-size: 1.5em;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike> NT$' . number_format($discounts->first()->discount).'</span>';
            } else {
                if ($discounts->first()->min === 1) {
                    return '<span style="color: #ee782e; font-size: 1.5em;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</span>';
                } else {
                    return '<span style="color: #ee782e; font-size: 1.5em;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</span>';
                }
            }
        }
    }

    public function indexPresentPrice($product)
    {
        $sku = $product->skus->first();
        $discounts = $sku->discounts;
        $regularPrice = $sku->regular_price;
        if(isset($sku->discount_start_at)) {
            $now = Carbon::now();
            $carbonDiscountStartAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_start_at);
            $carbonDiscountEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $sku->discount_end_at);

            if($carbonDiscountEndAt->gt($now) && $carbonDiscountStartAt->lte($now)){
                $html = '<span class="uk-label custom-label-1" style="color: #ee782e">優惠中</span><br><br>';
                if (count($discounts) === 0) {#防呆用，設置了時間但沒有優惠的情況
                    return '<span style="color: #333;">NT$' . number_format($regularPrice).'</span>';
                } elseif (count($discounts) === 1) {
                    $html .= '<span style="color: #ee782e;"><br><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike> NT$' . number_format($discounts->first()->discount).'</span>';
                    return $html;
                } else {
                    if ($discounts->first()->min === 1) {
                        $html .= '<span style="color: #ee782e;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</span>';
                        return $html;
                    } else {
                        $html .= '<span style="color: #ee782e;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</span>';
                        return $html;
                    }
                }
            } else {
                if($carbonDiscountEndAt->lte($now)) {
                    $html = '<span class="uk-label custom-label-2" style="color: #888">優惠結束</span><br><br>';
                } else {
                    $html = '<span class="uk-label custom-label-2" style="color: #888">優惠即將開始</span><br><br>';
                }

                if (count($discounts) === 0) {#防呆用，設置了時間但沒有優惠的情況
                    return '<span style="color: #333;">NT$' . number_format($regularPrice).'</span>';
                } elseif (count($discounts) === 1) {
                    $html .= '<span style="color: #333;">NT$' . number_format($regularPrice) . ' <strike style="color: #888;">NT$' . number_format($discounts->first()->discount).'</strike></span>';
                    return $html;
                } else {
                    if ($discounts->first()->min === 1) {
                        $html .= '<span style="color: #333;">NT$' . number_format($regularPrice) . '<br><strike style="color: #888;">NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</strike></span>';
                        return $html;
                    } else {
                        $html .= '<span style="color: #333;">NT$' . number_format($regularPrice) . '<br><strike style="color: #888;">NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</strike></span>';
                        return $html;
                    }
                }
            }
        } else {
            if (count($discounts) === 0) {
                return '<span style="color: #333;">NT$' . number_format($regularPrice).'</span>';
            } elseif (count($discounts) === 1) {
                return '<span style="color: #ee782e;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike> NT$' . number_format($discounts->first()->discount).'</span>';
            } else {
                if ($discounts->first()->min === 1) {
                    return '<span style="color: #ee782e;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($discounts->max('discount')).'</span>';
                } else {
                    return '<span style="color: #ee782e;"><strike style="color: #888;">NT$' . number_format($regularPrice) . '</strike><br>NT$'.number_format($discounts->min('discount')).' - NT$'.number_format($regularPrice).'</span>';
                }
            }
        }
    }

    public function presentTags($product)
    {
        $tags = $product->tags;
        if (count($tags) !== 0) {
            $html = '<div class="uk-margin">';
            foreach ($tags as $tag) {
                $html = $html.'<span class="uk-label custom-label-1" style="color: '.$tag->color.'">'.$tag->name.'</span>';
            }
            $html = $html.'</div>';
            return $html;
        } else {
            return '<div class="uk-margin"><span class="uk-label"></span></div>';
        }
    }

    public function hideInfo($str, $type)
    {   $strLength = mb_strlen( $str, "utf-8");
        if($type == 'name') {
            return mb_substr($str,0,$strLength-1,'utf8').str_repeat("*",1);
        } elseif( $type == 'phone') {
            $result = mb_substr($str,0,$strLength-3,'utf8');
            return str_pad($result, $strLength, "*", STR_PAD_RIGHT);
        } elseif( $type == 'email') {
            $atIndex = strpos($str, '@');
            $result = mb_substr($str, 0, $atIndex-2,'utf8');
            return str_pad($result, $strLength, "*", STR_PAD_RIGHT);
        } elseif( $type == 'address') {
            $result = mb_substr($str,0, 1,'utf8');
            return str_pad($result, $strLength, "*", STR_PAD_RIGHT);
        }
    }

    public function presentOrderStatus($deliveryStatus, $paymentStatus)
    {
        $transformedOrderStatusList = [
            #未付款     #直接付款 #取貨付款
            ['待付款', '待出貨', '待出貨'],
            ['error', '訂單完成', '訂單完成'],
            ['error', '已出貨', '已出貨'],
            ['error', '待退款', '待退款'],
            ['訂單取消', '已退款', '已退款'],
        ];
        return $transformedOrderStatusList[$deliveryStatus][$paymentStatus];
    }

    public function presentCoupons($coupons)
    {
        $html = '';
        foreach($coupons as $coupon) {
            #if(Carbon::createFromFormat('Y-m-d H:i:s', $coupon->end_at)->gt($now) && Carbon::createFromFormat('Y-m-d H:i:s', $coupon->start_at)->lte($now)){
            $html = $html.'<option value="'.$coupon->id.'" discount="'. $coupon->discount_price .'">'. $coupon->name .' - NT$'. number_format($coupon->discount_price) .'</option>';
        }
        return $html;
    }
}
