<?php

namespace App\Http\Controllers;

use App\CustomFacades\CustomClass;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    private $userService, $orderService, $couponService;

    public function __construct(UserService $userService, OrderService $orderService, CouponService $couponService)
    {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->couponService = $couponService;
    }

    public function showDashboard()
    {
        return CustomClass::viewWithTitle(view('account.dashboard'), '會員中心');
    }

    public function editProfile()
    {
        $user = Auth::user();
        return CustomClass::viewWithTitle(view('account.profile.edit')->with('user', $user), '會員資料');
    }

    public function updateProfile(Request $request)
    {
        $input = $request->all();
        $rules = [
            'email' => 'unique:users|max:255|nullable',
            'phone' => 'required',
            'birthday' => 'required',
            'county' => 'required',
            'district' => 'required',
            'address' => 'required'
        ];
        $messages = [
            'email.unique'=>'電子郵件已被使用過，請換一個後再試',
            'phone.required'=>'需填寫電話號碼',
            'birthday.required'=>'需填寫生日',
            'county.required'=>'需選擇縣市',
            'district.required'=>'需選擇鄉鎮',
            'address.required'=>'需填寫地址',
        ];
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $this->userService->updateAccount($request);
        $this->couponService->assignBirthdayCoupons(Auth::user()->id);

        return back()->with('Success', '保存成功');
    }

    public function indexOrders()
    {
        $orders = $this->orderService->getAllOrdersByUser();
        return CustomClass::viewWithTitle(view('account.orders.index')->with('orders', $orders), '訂單');
    }

    public function showOrder($orderId)
    {
        $order = $this->orderService->getOrder($orderId);
        if(isset($order->user_id)) {
            if (Auth::check()) {
                if(Auth::user()->id === $order->user_id) {
                    return CustomClass::viewWithTitle(view('account.orders.show')->with('order', $order), '訂單');
                } else {
                    return redirect()->route('warning')->with('Warning', '沒有權限');
                }
            } else {
                return redirect()->route('warning')->with('Warning', '沒有權限');
            }

        } else {
            return CustomClass::viewWithTitle(view('account.orders.show')->with('order', $order), '訂單');
        }
    }

    public function indexCoupons()
    {
        $coupons = $this->couponService->getAllCouponsByUser();
        return CustomClass::viewWithTitle(view('account.coupons.index')->with('coupons', $coupons), '優惠券');
    }
}
