<?php

namespace App\Http\Controllers;

use App\CustomFacades\CustomClass;
use App\Jobs\AssignBirthdayCoupon;
use App\Models\Coupon;
use App\Services\CategoryService;
use App\Services\CouponService;
use App\Services\EcPayService;
use App\Services\InviteCodeService;
use App\Services\LineService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\TagService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private $categoryService;
    private $tagService;
    private $productService;
    private $orderService;
    private $ecPayService;
    private $couponService;
    private $userService;
    private $inviteCodeService;
    private $lineService;

    public function __construct(CategoryService $categoryService, TagService $tagService, ProductService $productService, OrderService $orderService, EcPayService $ecPayService, CouponService $couponService, UserService $userService, InviteCodeService $inviteCodeService, LineService $lineService)
    {
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->productService = $productService;
        $this->orderService = $orderService;
        $this->ecPayService = $ecPayService;
        $this->couponService = $couponService;
        $this->userService = $userService;
        $this->inviteCodeService = $inviteCodeService;
        $this->lineService = $lineService;
    }

    public function showDashboard()
    {
        /*// 查询今年每月的成功订单总计
        $monthlyTotals = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->where('delivery_status', 2)
            ->whereIn('payment_status', [1, 2])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month');

        // 格式化数据，使其包含所有月份
        $formattedData = array_fill(1, 12, 0);
        foreach ($monthlyTotals as $month => $total) {
            $formattedData[$month] = $total;
        }

        // 计算今年目前为止的总计
        $totalSoFar = array_sum($formattedData);

        // 计算从网站创建以来的总计
        $totalOverall = DB::table('orders')
            ->where('delivery_status', 2)
            ->whereIn('payment_status', [1, 2])
            ->sum('total');

        return view('admin.dashboard', [
            'monthlyTotals' => $formattedData,
            'totalSoFar' => $totalSoFar,
            'totalOverall' => $totalOverall
        ]);*/
        return view('admin.dashboard');
    }

    public function createProduct()
    {
        $rootCategories = $this->categoryService->getRoots();
        $tags = $this->tagService->getAllTags();
        return CustomClass::viewWithTitle(view('admin.products.create')->with('rootCategories', $rootCategories)->with('tags', $tags), '建立商品');
    }

    public function storeProduct(Request $request)
    {#dd($request->all());
        $input = $request->all();
        $rules = [
            'name' => 'required',
            'regular_price' => 'required',
            'mins.*' => 'required',
            'maxs.*' => 'required',
            'discount_price.*' => 'required'
        ];

        $messages = [
            'name.required' => '需填寫商品名稱',
            'regular_price.required' => '需填寫價格',
            'mins.*.required' => '需填寫最小數量',
            'maxs.*.required' => '需填寫最大數量',
            'discount_price.*.required' => '需填寫折扣價格'
        ];

        if (isset($request->discount_start_at) || isset($request->discount_end_at)) {
            $rules['discount_start_at'] = 'required';
            $rules['discount_end_at'] = 'required';
            $messages['discount_start_at.required'] = '需填寫起始時間';
            $messages['discount_end_at.required'] = '需填寫結束時間';
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            $product = $this->productService->storeProduct($request);
            return response()->json(['success' => $product->id]);
        }
    }

    public function editProduct($productId)
    {
        $product = $this->productService->getProduct($productId);
        $skus = $product->skus;
        $rootCategories = $this->categoryService->getRoots();
        $tags = $this->tagService->getAllTags();
        return CustomClass::viewWithTitle(view('admin.products.edit')->with('product', $product)->with('skus', $skus)->with('rootCategories', $rootCategories)->with('tags', $tags), '編輯商品');
    }

    public function updateProduct(Request $request, $productId)
    {
        $input = $request->all();

        $rules = [
            'name' => 'required',
            'regular_price' => 'required',
            'mins.*' => 'required',
            'maxs.*' => 'required',
            'discount_price.*' => 'required'
        ];

        $messages = [
            'name.required' => '需填寫商品名稱',
            'regular_price.required' => '需填寫價格',
            'mins.*.required' => '需填寫最小數量',
            'maxs.*.required' => '需填寫最大數量',
            'discount_price.*.required' => '需填寫折扣價格'
        ];

        if (isset($request->discount_start_at) || isset($request->discount_end_at)) {
            $rules['discount_start_at'] = 'required';
            $rules['discount_end_at'] = 'required';
            $messages['discount_start_at.required'] = '需填寫起始時間';
            $messages['discount_end_at.required'] = '需填寫結束時間';
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            $this->productService->updateProduct($request, $productId);
            return response()->json(['success' => $productId]);
        }
    }

    public function showProducts()
    {#dd($this->productService->getProduct(1)->categories()->pluck('name')->implode(' '));
        return CustomClass::viewWithTitle(view('admin.products.index'), '管理商品');
    }

    public function ajaxGetVisibleProducts($visible)
    {
        return $this->productService->ajaxGetProducts($visible);
    }

    public function ajaxSearchSku(Request $request)
    {
        #dd($request->keyword);
        return $this->productService->ajaxSearchSkuByName($request->keyword);
    }

    public function createCategory()
    {
        $rootCategories = $this->categoryService->getRoots();
        return CustomClass::viewWithTitle(view('admin.categories.create')->with('rootCategories', $rootCategories), '建立分類');
    }

    public function storeCategory(Request $request)
    {
        $this->categoryService->storeCategory($request);
        return redirect()->route('admin.categories.index')->with('Success', '建立成功');
    }

    public function showCategories()
    {
        $categories = $this->categoryService->getAllCategories();
        return CustomClass::viewWithTitle(view('admin.categories.index')->with('categories', $categories), '管理分類');
    }

    public function editCategory($categoryId)
    {
        $categories = $this->categoryService->getAllCategories();
        $category = $this->categoryService->getCategory($categoryId);
        return CustomClass::viewWithTitle(view('admin.categories.edit')->with('category', $category)->with('categories', $categories), '管理分類');
    }

    public function updateCategory(Request $request, $categoryId)
    {
        $this->categoryService->updateCategory($request, $categoryId);
        return back()->with('Success', '修改成功');
    }

    public function createTag()
    {
        return CustomClass::viewWithTitle(view('admin.tags.create'), '建立標籤');
    }

    public function storeTag(Request $request)
    {
        $this->tagService->storeTag($request);
        return redirect()->route('admin.tags.index')->with('Success', '建立成功');
    }

    public function showTags()
    {
        $tags = $this->tagService->getAllTags();
        return CustomClass::viewWithTitle(view('admin.tags.index')->with('tags', $tags), '管理標籤');
    }

    public function editTag($tagId)
    {
        $tag = $this->tagService->getTag($tagId);
        return CustomClass::viewWithTitle(view('admin.tags.edit')->with('tag', $tag), '管理標籤');
    }

    public function updateTag(Request $request, $tagId)
    {
        $this->tagService->updateTag($request, $tagId);
        return back()->with('Success', '修改成功');
    }

    public function manageOrders()
    {
        $typeOrders = $this->orderService->getOrdersByAdmin();
        return CustomClass::viewWithTitle(view('admin.orders.manage')->with('notPaidOrders', $typeOrders[0])->with('waitDeliverOrders', $typeOrders[1])->with('deliveredOrders', $typeOrders[2]), '管理訂單');
    }

    public function indexOrders()
    {
        return CustomClass::viewWithTitle(view('admin.orders.index'), '全部訂單');
    }

    public function ajaxGetAllOrders()
    {
        return $this->orderService->ajaxGetAllOrders();
    }

    public function showOrder($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        return CustomClass::viewWithTitle(view('admin.orders.show')->with('order', $order), '訂單');
    }

    public function printCvsOrder($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        $this->ecPayService->printCvsOrder($order);
    }

    public function updateOrder(Request $request, $orderId)
    {
        if (isset($request->refundMethod)) {
            $this->refund($request, $orderId);
        }
        $this->orderService->updateOrder($orderId, $request);
        return redirect()->back();
    }

    public function cancelOrder($orderId)
    {
        $this->orderService->cancelOrder($orderId);
        $this->reduction_IC_CP_PD($orderId);
        return redirect()->back();
    }

    public function refund($request, $orderId)
    {
        $refundMethod = $request->refundMethod;
        if ($refundMethod == 'point') {
            $this->orderService->pointRefund($request, $orderId);
        } elseif ($refundMethod == 'linePay') {
            $this->lineService->refund($request, $orderId);
        } else {
            $this->orderService->otherRefund($request, $orderId);
        }
        $this->reduction_IC_CP_PD($orderId);
    }

    public function createCoupon()
    {
        return CustomClass::viewWithTitle(view('admin.coupons.create'), '建立優惠券');
    }

    public function storeCoupon(Request $request)
    {
        $coupon = $this->couponService->storeCoupon($request);
        return redirect()->route('admin.coupons.index', $coupon)->with('Success', '建立成功');
    }

    public function indexCoupons()
    {
        $coupons = $this->couponService->getAllCoupons()->where('expired', 0);
        return CustomClass::viewWithTitle(view('admin.coupons.index')->with('coupons', $coupons), '管理優惠券');
    }

    public function editCoupon($couponId)
    {

        $coupon = $this->couponService->getCoupon($couponId);
        return CustomClass::viewWithTitle(view('admin.coupons.edit')->with('coupon', $coupon), '編輯優惠券');
    }

    public function updateCoupon(Request $request, $couponId)
    {
        $this->couponService->updateCoupon($request, $couponId);
        return back()->with('Success', '修改成功');
    }

    public function assignCoupon(Request $request, $couponId)
    {
        $allUsers = $this->userService->all();
        $this->couponService->assignCoupon($allUsers, $couponId);
    }

    public function createInviteCode()
    {
        return CustomClass::viewWithTitle(view('admin.invite_codes.create'), '建立邀請碼');
    }

    public function storeInviteCode(Request $request)
    {
        $inviteCode = $this->inviteCodeService->storeinviteCode($request);
        return redirect()->route('admin.invite_codes.index', $inviteCode)->with('Success', '建立成功');
    }

    public function indexInviteCodes()
    {
        $allInviteCodes = $this->inviteCodeService->getAllInviteCodes();
        return CustomClass::viewWithTitle(view('admin.invite_codes.index')->with('allInviteCodes', $allInviteCodes), '管理邀請碼');
    }

    public function indexUsers()
    {
        return CustomClass::viewWithTitle(view('admin.users.index'), '查看會員');
    }

    public function ajaxGetAllUsers()
    {
        return $this->userService->ajaxGetAllUsers();
    }

    public function deleteProducts(Request $request)
    {
        if (isset($request->productIds)) {
            $this->productService->deleteProducts($request->productIds);
        }
        return back();
    }

    public function reduction_IC_CP_PD($orderId)#Invite Code, Coupon, Point Discount
    {
        $order = $this->orderService->getOrder($orderId);
        if ($order->invite_code !== null) {
            $this->inviteCodeService->reductionInviteCode($order->invite_code, $order->total);
        }
        if ($order->coupon_id !== null) {
            $this->couponService->reductionCoupon($order->user_id, $order->coupon_id);
        }
        if ($order->point_discount !== null) {
            $this->userService->reductionPoint($order->user_id, $order->point_discount);
        }
    }

    public function test()
    {
        #dd(Coupon::find(26)->startAtDateMin);
        #AssignBirthdayCoupon::dispatch();
        $unExpiredCoupons = Coupon::where('expired', 0)->get();
        foreach ($unExpiredCoupons as $unExpiredCoupon) {
            if (Carbon::now()->gt($unExpiredCoupon->end_at)) {
                $unExpiredCoupon->update(['expired' => 1]);
            }
        }
    }
}
