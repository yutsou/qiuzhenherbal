<?php

return [
    'double_11' => [
        'enabled' => env('DOUBLE_11_ENABLED', true),

        // 活動時間：11/6～11/15
        'start_at' => env('DOUBLE_11_START_AT', date('Y') . '-10-11 00:00:00'),
        'end_at' => env('DOUBLE_11_END_AT', date('Y') . '-11-15 23:59:59'),

        // 設定內容：滿 1500 送 1 張 100 折價券，滿 3000 送兩張，以此類推
        'reward_threshold' => 1500,
        'reward_discount_price' => 100,

        // 贈送的折價券使用方法：每滿 1,000 元可用一張 100 元券；滿 2,000 元可用兩張，以此類推
        'coupon_name' => '雙11滿千折百券',
        'coupon_min_spend' => 1000,
        'coupon_is_accumulative' => true,

        // 使用期限：90 天內
        'coupon_valid_days' => 90,
    ],
];

