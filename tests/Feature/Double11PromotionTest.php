<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Services\CouponService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class Double11PromotionTest extends TestCase
{
    use DatabaseTransactions;

    protected $couponService;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = app(CouponService::class);
        $this->orderService = app(OrderService::class);
    }

    public function test_reward_double_11_coupons_within_event_period()
    {
        $user = User::factory()->create();

        // 模擬當前時間在活動期間內（例如 11/10）
        Carbon::setTestNow(Carbon::create(2026, 11, 10, 12, 0, 0));

        Config::set('promotions.double_11.enabled', true);
        Config::set('promotions.double_11.start_at', '2026-11-06 00:00:00');
        Config::set('promotions.double_11.end_at', '2026-11-15 23:59:59');
        Config::set('promotions.double_11.reward_threshold', 1500);
        Config::set('promotions.double_11.reward_discount_price', 100);
        Config::set('promotions.double_11.coupon_valid_days', 90);

        // 測試 1: 實付 1500（總額 1560，運費 60）
        // 尚未完成訂單時（delivery_status = 0），不應發券
        $order1 = Order::create([
            'user_id' => $user->id,
            'subtotal' => 1500,
            'delivery_fee' => 60,
            'total' => 1560,
            'payment_status' => 1,
            'delivery_status' => 0,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);

        $this->couponService->rewardDouble11Coupons($order1);
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 訂單完成時（delivery_status = 1），應送 1 張 100 折價券
        $order1->update(['delivery_status' => 1]);
        $this->couponService->rewardDouble11Coupons($order1->fresh());

        $coupons = $user->fresh()->unusedUnexpiredCoupons;
        $this->assertCount(1, $coupons);
        $coupon = $coupons->first();
        $this->assertEquals(100, intval($coupon->discount_price));
        $this->assertEquals(1000, intval($coupon->min_spend));
        $this->assertTrue($coupon->is_accumulative);
        $this->assertEquals(3, $coupon->type);
        $this->assertEquals($order1->id, $coupon->order_id);
        $this->assertEquals(Carbon::now()->addDays(90)->endOfDay()->toDateTimeString(), $coupon->end_at->toDateTimeString());

        // 測試防重複發放（冪等性）
        $this->couponService->rewardDouble11Coupons($order1->fresh());
        $this->assertCount(1, $user->fresh()->unusedUnexpiredCoupons);

        // 測試 2: 實付 3200（總額 3260，運費 60），完成訂單後應再送 2 張
        $order2 = Order::create([
            'user_id' => $user->id,
            'subtotal' => 3200,
            'delivery_fee' => 60,
            'total' => 3260,
            'payment_status' => 1,
            'delivery_status' => 1,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);

        $this->couponService->rewardDouble11Coupons($order2);

        $couponsAfter = $user->fresh()->unusedUnexpiredCoupons;
        // 原有 1 張 + 新增 2 張 = 共 3 張
        $this->assertCount(3, $couponsAfter);

        Carbon::setTestNow();
    }

    public function test_do_not_reward_coupons_outside_event_period_or_under_threshold()
    {
        $user = User::factory()->create();

        Config::set('promotions.double_11.enabled', true);
        Config::set('promotions.double_11.start_at', '2026-11-06 00:00:00');
        Config::set('promotions.double_11.end_at', '2026-11-15 23:59:59');

        // 情況 A: 活動開始前下單（11/5），即使完成訂單也不發券
        Carbon::setTestNow(Carbon::create(2026, 11, 5, 23, 59, 59));
        $orderA = Order::create([
            'user_id' => $user->id,
            'subtotal' => 2000,
            'delivery_fee' => 60,
            'total' => 2060,
            'payment_status' => 1,
            'delivery_status' => 1,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);
        $this->couponService->rewardDouble11Coupons($orderA);
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 情況 B: 活動結束後下單（11/16），完成訂單不發券
        Carbon::setTestNow(Carbon::create(2026, 11, 16, 0, 0, 1));
        $orderB = Order::create([
            'user_id' => $user->id,
            'subtotal' => 2000,
            'delivery_fee' => 60,
            'total' => 2060,
            'payment_status' => 1,
            'delivery_status' => 1,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);
        $this->couponService->rewardDouble11Coupons($orderB);
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 情況 C: 活動期間內下單，但實付金額未達 1500（例如 1450 + 運費 60 = 1510）
        Carbon::setTestNow(Carbon::create(2026, 11, 10, 12, 0, 0));
        $orderC = Order::create([
            'user_id' => $user->id,
            'subtotal' => 1450,
            'delivery_fee' => 60,
            'total' => 1510,
            'payment_status' => 1,
            'delivery_status' => 1,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);
        $this->couponService->rewardDouble11Coupons($orderC);
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 情況 D: 非會員（訪客）即使完成訂單也不發券
        $guestOrder = Order::create([
            'user_id' => null,
            'subtotal' => 3000,
            'delivery_fee' => 60,
            'total' => 3060,
            'payment_status' => 1,
            'delivery_status' => 1,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Guest',
            'receiver_cell_phone' => '0912345678',
        ]);
        $this->couponService->rewardDouble11Coupons($guestOrder);
        $this->assertEquals(0, Coupon::where('order_id', $guestOrder->id)->count());

        Carbon::setTestNow();
    }

    public function test_coupon_usage_with_threshold_and_accumulation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 建立 2 張雙11滿千折百券給該使用者
        $c1 = Coupon::create([
            'type' => 3,
            'name' => '雙11滿千折百券',
            'discount_price' => 100,
            'min_spend' => 1000,
            'is_accumulative' => true,
            'assigned' => 1,
            'usage_count' => 0,
            'expired' => 0,
            'start_at' => Carbon::now()->startOfDay(),
            'end_at' => Carbon::now()->addDays(90)->endOfDay(),
        ]);
        $c2 = Coupon::create([
            'type' => 3,
            'name' => '雙11滿千折百券',
            'discount_price' => 100,
            'min_spend' => 1000,
            'is_accumulative' => true,
            'assigned' => 1,
            'usage_count' => 0,
            'expired' => 0,
            'start_at' => Carbon::now()->startOfDay(),
            'end_at' => Carbon::now()->addDays(90)->endOfDay(),
        ]);

        $user->coupons()->attach([$c1->id, $c2->id]);
        $this->assertCount(2, $user->fresh()->unusedUnexpiredCoupons);

        // 測試 1: 金額未滿 1000（subtotal = 800），使用 1 張應拋出 400 錯誤
        $requestUnderThreshold = new Request([
            'subtotal' => 800,
            'deliveryFee' => 60,
            'coupon' => $c1->id,
            'couponCount' => 1,
            'couponDiscount' => 100,
            'total' => 760,
            'logisticsType' => 'home-delivery',
            'paymentMethod' => 'COD',
            'receiverName' => 'Tester',
            'receiverCellPhone' => '0912345678',
            'itemNames' => [],
            'skuIds' => [],
            'discountPrices' => [],
            'quantities' => [],
            'productSubtotals' => [],
        ]);

        $failed = false;
        try {
            $this->orderService->storeOrder($requestUnderThreshold);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $failed = true;
        }
        $this->assertTrue($failed);

        // 測試 2: 金額滿 1500，使用 2 張（需滿 2000）應拋出 400 錯誤
        $requestInvalidCount = new Request([
            'subtotal' => 1500,
            'deliveryFee' => 60,
            'coupon' => $c1->id,
            'couponCount' => 2,
            'couponDiscount' => 200,
            'total' => 1360,
            'logisticsType' => 'home-delivery',
            'paymentMethod' => 'COD',
            'receiverName' => 'Tester',
            'receiverCellPhone' => '0912345678',
            'itemNames' => [],
            'skuIds' => [],
            'discountPrices' => [],
            'quantities' => [],
            'productSubtotals' => [],
        ]);

        $failed2 = false;
        try {
            $this->orderService->storeOrder($requestInvalidCount);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $failed2 = true;
        }
        $this->assertTrue($failed2);

        // 測試 3: 金額滿 2200，使用 2 張應成功，且扣抵 200 元
        $requestValid = new Request([
            'subtotal' => 2200,
            'deliveryFee' => 60,
            'coupon' => $c1->id,
            'couponCount' => 2,
            'couponDiscount' => 200,
            'total' => 2060,
            'logisticsType' => 'home-delivery',
            'paymentMethod' => 'COD',
            'receiverName' => 'Tester',
            'receiverCellPhone' => '0912345678',
            'itemNames' => [],
            'skuIds' => [],
            'discountPrices' => [],
            'quantities' => [],
            'productSubtotals' => [],
        ]);

        $order = $this->orderService->storeOrder($requestValid);
        $this->assertEquals(200, intval($order->coupon_discount));
        $this->assertEquals(2, $order->coupon_count);
        $this->assertEquals('雙11滿千折百券 x2', $order->coupon_name);

        // 執行扣券
        $this->couponService->useCoupon($order->coupon_id, $user, $order->coupon_count);

        // 驗證 2 張優惠券都被扣除，有效券剩 0 張
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 測試 4: 取消/退款時還券
        $this->couponService->reductionCoupon($user->id, $order->coupon_id, $order->coupon_count);
        $this->assertCount(2, $user->fresh()->unusedUnexpiredCoupons);
    }

    public function test_present_coupons_groups_accumulative_coupons()
    {
        $user = User::factory()->create();
        $c1 = Coupon::create([
            'type' => 3,
            'name' => '雙11滿千折百券',
            'discount_price' => 100,
            'min_spend' => 1000,
            'is_accumulative' => true,
            'assigned' => 1,
            'usage_count' => 0,
            'expired' => 0,
            'start_at' => Carbon::now()->startOfDay(),
            'end_at' => Carbon::now()->addDays(90)->endOfDay(),
        ]);
        $c2 = Coupon::create([
            'type' => 3,
            'name' => '雙11滿千折百券',
            'discount_price' => 100,
            'min_spend' => 1000,
            'is_accumulative' => true,
            'assigned' => 1,
            'usage_count' => 0,
            'expired' => 0,
            'start_at' => Carbon::now()->startOfDay(),
            'end_at' => Carbon::now()->addDays(90)->endOfDay(),
        ]);
        $user->coupons()->attach([$c1->id, $c2->id]);

        $presenter = new \App\Presenters\ShopPresenter();
        $html = $presenter->presentCoupons($user->unusedUnexpiredCoupons);

        $this->assertStringContainsString('雙11滿千折百券 (每滿NT$1,000折NT$100，持有 2 張)', $html);
        $this->assertStringContainsString('is-accumulative="1"', $html);
        $this->assertStringContainsString('min-spend="1000"', $html);
        $this->assertStringContainsString('holding-count="2"', $html);
        $this->assertStringContainsString('discount="100"', $html);
    }

    public function test_checkout_cod_flow_rewards_double_11_coupons_only_upon_completion()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Carbon::setTestNow(Carbon::create(2026, 11, 10, 12, 0, 0));
        Config::set('promotions.double_11.enabled', true);
        Config::set('promotions.double_11.start_at', '2026-11-06 00:00:00');
        Config::set('promotions.double_11.end_at', '2026-11-15 23:59:59');

        $payload = [
            'subtotal' => 1500,
            'deliveryFee' => 60,
            'inviteCodeDiscount' => null,
            'couponDiscount' => null,
            'pointDiscount' => null,
            'total' => 1560,
            'logisticsType' => 'home-delivery',
            'logisticsSubType' => '',
            'paymentMethod' => 'COD',
            'receiverName' => 'Tester',
            'receiverCellPhone' => '0912345678',
            'receiverEmail' => 'tester@example.com',
            'county' => '台北市',
            'district' => '中正區',
            'zipcode' => '100',
            'address' => '重慶南路一段',
            'inviteCode' => null,
            'coupon' => null,
            'remark' => 'Test COD',
            'itemNames' => [],
            'skuIds' => [],
            'discountPrices' => [],
            'quantities' => [],
            'productSubtotals' => [],
        ];

        $response = $this->postJson(route('shop.checkout.check_out'), $payload);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('1', $data[0]); // COD success code

        $order = Order::where('user_id', $user->id)->latest()->first();
        $this->assertEquals(0, $order->delivery_status);

        // 結帳當下尚未完成訂單，不應發放折價券
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 步驟 1: 出貨（通知出貨後 delivery_status 變 2），仍未完成，不發券
        $this->orderService->updateOrder($order->id, new \Illuminate\Http\Request());
        $this->assertEquals(2, $order->fresh()->delivery_status);
        $this->assertCount(0, $user->fresh()->unusedUnexpiredCoupons);

        // 步驟 2: 完成訂單（delivery_status 變 1），此時才正式發放 1 張雙11折價券
        $this->orderService->updateOrder($order->id, new \Illuminate\Http\Request());
        $this->assertEquals(1, $order->fresh()->delivery_status);

        $coupons = $user->fresh()->unusedUnexpiredCoupons;
        $this->assertCount(1, $coupons);
        $this->assertEquals('雙11滿千折百券', $coupons->first()->name);

        Carbon::setTestNow();
    }

    public function test_order_created_during_event_completed_after_event_still_gets_coupon()
    {
        $user = User::factory()->create();

        Config::set('promotions.double_11.enabled', true);
        Config::set('promotions.double_11.start_at', '2026-11-06 00:00:00');
        Config::set('promotions.double_11.end_at', '2026-11-15 23:59:59');

        // 11/14 下單（活動期間內）
        Carbon::setTestNow(Carbon::create(2026, 11, 14, 15, 0, 0));
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => 1500,
            'delivery_fee' => 60,
            'total' => 1560,
            'payment_status' => 1,
            'delivery_status' => 2,
            'logistics_type' => 'home-delivery',
            'payment_method' => 'creditCard',
            'receiver_name' => 'Tester',
            'receiver_cell_phone' => '0912345678',
        ]);

        // 11/18 送達並完成訂單（雖然活動日期已過，但下單於活動內，應正常發券）
        Carbon::setTestNow(Carbon::create(2026, 11, 18, 10, 0, 0));
        $this->orderService->updateOrder($order->id, new \Illuminate\Http\Request());

        $this->assertEquals(1, $order->fresh()->delivery_status);
        $this->assertCount(1, $user->fresh()->unusedUnexpiredCoupons);

        Carbon::setTestNow();
    }
}
