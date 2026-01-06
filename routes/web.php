<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\EnsureEmailIsValid;
use App\Http\Middleware\EnsureProductIsVisible;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MemberController;
use App\CustomFacades\CustomClass;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ShopController::class, 'showHomePage']);

#auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.show');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::post('/reset-password/request', [AuthController::class, 'sendResetPasswordConfirmation'])->name('password.reset_password_confirmation.send');
Route::get('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/password/set', [AuthController::class, 'setPassword'])->name('password.set');
Route::get('/email/verify', [AuthController::class, 'verifyEmail'])->name('account.email.verify');

Route::get('/auth/line/login', [AuthController::class, 'redirectLineLogin'])->name('line.login');
Route::get('/auth/line/callback', [AuthController::class, 'lineCallback']);

Route::get('/auth/facebook/login', [AuthController::class, 'redirectFacebookLogin'])->name('facebook.login');
Route::get('/auth/facebook/callback', [AuthController::class, 'facebookCallback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'switchRole']);
    Route::get('/account/email-verification/request', [AuthController::class, 'showEmailVerificationRequest'])->name('account.email_verification.request');
    Route::get('/account/email-verification/send', [AuthController::class, 'sendEmailVerification'])->name('account.email_verification.send');
});

Route::prefix('account')->middleware(['auth', EnsureEmailIsValid::class])->group(function () {
    Route::get('/dashboard', [MemberController::class, 'showDashboard'])->name('account.dashboard.show');
    Route::get('/edit', [MemberController::class, 'editProfile'])->name('account.profile.edit');
    Route::post('/account', [MemberController::class, 'updateProfile'])->name('account.profile.update');

    Route::get('/password/edit', [AuthController::class, 'editPassword'])->name('account.password.edit');
    Route::post('/password', [AuthController::class, 'updatePassword'])->name('account.password.update');

    Route::get('/orders', [MemberController::class, 'indexOrders'])->name('account.orders.index');
    Route::get('/coupons', [MemberController::class, 'indexCoupons'])->name('account.coupons.index');

    Route::get('/orders/{orderId}', [MemberController::class, 'showOrder'])->name('account.orders.show');
});

Route::get('/warning', [ShopController::class, 'showWarning'])->name('warning');

Route::prefix('admin/dashboard')->middleware(EnsureIsAdmin::class)->group(function () {
    Route::get('/', [AdminController::class, 'showDashboard'])->name('admin.dashboard');

    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products', [AdminController::class, 'showProducts'])->name('admin.products.index');
    Route::get('/products/{productId}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::post('/products/{productId}', [AdminController::class, 'updateProduct'])->name('admin.products.update');

    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories', [AdminController::class, 'showCategories'])->name('admin.categories.index');
    Route::get('/categories/{categoryId}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::post('/categories/{categoryId}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');

    Route::get('/tags/create', [AdminController::class, 'createTag'])->name('admin.tags.create');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('admin.tags.store');
    Route::get('/tags', [AdminController::class, 'showTags'])->name('admin.tags.index');
    Route::get('/tags/{tagId}/edit', [AdminController::class, 'editTag'])->name('admin.tags.edit');
    Route::post('/tags/{tagId}', [AdminController::class, 'updateTag'])->name('admin.tags.update');

    Route::get('/orders/manage', [AdminController::class, 'manageOrders'])->name('admin.orders.manage');
    Route::get('/orders', [AdminController::class, 'indexOrders'])->name('admin.orders.index');
    Route::get('/orders/{orderId}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/orders/{orderId}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
    Route::post('/orders/{orderId}/cancel', [AdminController::class, 'cancelOrder'])->name('admin.orders.cancel');


    Route::get('/coupons/create', [AdminController::class, 'createCoupon'])->name('admin.coupons.create');
    Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
    Route::get('/coupons/{couponId}', [AdminController::class, 'editCoupon'])->name('admin.coupons.edit');
    Route::post('/coupons/{couponId}', [AdminController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::get('/coupons', [AdminController::class, 'indexCoupons'])->name('admin.coupons.index');

    Route::get('/invite-codes/create', [AdminController::class, 'createInviteCode'])->name('admin.invite_codes.create');
    Route::post('/invite-codes', [AdminController::class, 'storeInviteCode'])->name('admin.invite_codes.store');
    Route::get('/invite-codes', [AdminController::class, 'indexInviteCodes'])->name('admin.invite_codes.index');

    Route::get('/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');

});
Route::prefix('admin')->middleware(EnsureIsAdmin::class)->group(function () {
    Route::get('/ajax/all-products/{visible}', [AdminController::class, 'ajaxGetVisibleProducts'])->name('admin.ajax.visible_products');
    Route::get('/ajax/all-orders', [AdminController::class, 'ajaxGetAllOrders'])->name('admin.ajax.all_orders');
    Route::post('/ajax/search/sku', [AdminController::class, 'ajaxSearchSku'])->name('admin.ajax.search.sku');
    Route::get('/orders/{orderId}/print', [AdminController::class, 'printCvsOrder'])->name('admin.orders.print');
    Route::post('/ajax/assign/coupons/{couponId}', [AdminController::class, 'assignCoupon']);
    Route::get('/ajax/all-users', [AdminController::class, 'ajaxGetAllUsers'])->name('admin.ajax.all_users');
    Route::post('/products', [AdminController::class, 'deleteProducts'])->name('admin.products.delete');
    Route::get('/test', [AdminController::class, 'test']);
});

Route::get('/products', [ShopController::class, 'indexProducts'])->name('shop.products.index');
Route::get('/products/{productId}', [ShopController::class, 'showProduct'])->middleware(EnsureProductIsVisible::class)->name('shop.products.show');
Route::get('/cart', [ShopController::class, 'showCart'])->name('shop.cart.show');
Route::post('/ajax/valid-inventory', [ShopController::class, 'ajaxValidInventory'])->name('ajax.inventory.valid');

Route::get('/delivery-fee/calculate', [ShopController::class, 'showDeliveryFeeCalculate'])->name('shop.delivery_fee.calculate');
Route::post('/delivery-detailed/submit', [ShopController::class, 'submitDeliveryDetailed'])->name('shop.delivery_detailed.submit');
Route::post('/ajax/delivery-detailed/valid', [ShopController::class, 'ajaxValidDeliveryDetailed'])->name('ajax.delivery_detailed.valid');

Route::get('/checkout', [ShopController::class, 'showCheckout'])->name('shop.checkout.show');
Route::post('/check-out', [ShopController::class, 'checkout'])->name('shop.checkout.check_out');

Route::post('/ajax/flashCart', [ShopController::class, 'ajaxFlashCart'])->name('shop.ajax.flash_cart');
Route::get('/ajax/getInviteCodeDiscount/{inviteCode}', [ShopController::class, 'ajaxGetInviteCodeDiscount'])->name('shop.ajax.get_invite_code_discount');
Route::post('/ajax/carts',[ShopController::class, 'ajaxStoreCart'])->name('shop.ajax.carts.store');
Route::post('/ajax/cookie-carts',[ShopController::class, 'ajaxStoreCookieCart'])->name('shop.ajax.cookie_carts.store');
Route::post('/ajax/update/carts',[ShopController::class, 'ajaxUpdateCart'])->name('shop.ajax.update_cart');
Route::post('/delete/carts/{skuId}', [ShopController::class, 'deleteCart'])->name('shop.carts.delete');



Route::post('/map/ecpay/server-reply', [ShopController::class, 'mapEcpayServerReply'])->name('shop.map.ecpay.server_reply');
Route::post('/cvs-order/ecpay/server-reply', [ShopController::class, 'cvsOrderEcpayServerReply'])->name('shop.cvs_order.ecpay.server_reply');
Route::post('/cvs-order/ecpay/client-reply', [ShopController::class, 'cvsOrderEcpayClientReply'])->name('shop.cvs_order.ecpay.client_reply');
Route::post('/cvs-order/ecpay/issue-reply', [ShopController::class, 'cvsOrderEcpayIssueReply'])->name('shop.cvs_order.ecpay.issue_reply');
Route::post('/pay/ecpay/receive', [ShopController::class, 'payEcpayReceive'])->name('shop.pay.ecpay.receive');
Route::post('/pay/ecpay/order-receive', [ShopController::class, 'payEcpayOrderReceive'])->name('shop.pay.ecpay.order_receive');

Route::get('/pay/{orderId}', [ShopController::class, 'pay'])->name('shop.pay');
Route::post('/repay/{orderId}', [ShopController::class, 'repay'])->name('shop.repay');

Route::get('/pay/line/receive', [ShopController::class, 'payLineReceive'])->name('shop.pay.line.receive');
Route::get('/pay/line/result/{orderId}', [ShopController::class, 'showPayLineResult'])->name('shop.pay.line.result');
Route::get('/ajax/pay/line/getPaymentStatus/{orderId}', [ShopController::class, 'getOrderPaymentStatus'])->name('shop.ajax.get.payment_status');

Route::get('/about-us', function(){
    return CustomClass::viewWithTitle(view('about_us'), '關於我們');
});

Route::get('/contract-us', function(){
    return CustomClass::viewWithTitle(view('contract_us'), '聯絡我們');
});

Route::get('/privacy-policy', function(){
    return CustomClass::viewWithTitle(view('privacy_policy'), '隱私權政策');
});

Route::get('/terms', function(){
    return CustomClass::viewWithTitle(view('terms'), '會員服務條款');
});

Route::get('/invoice-processing-procedure', function(){
    return CustomClass::viewWithTitle(view('invoice_processing_procedure'), '發票處理程序');
});

Route::get('/delivery-method-and-fee-calculation', function(){
    return CustomClass::viewWithTitle(view('delivery_method_and_fee_calculation'), '配送方式與運費計算');
});

Route::get('/return-or-exchange-and-refund-method', function(){
    return CustomClass::viewWithTitle(view('return_or_exchange_and_refund_method'), '退換貨及退款方式');
});

Route::get('/payment-method', function(){
    return CustomClass::viewWithTitle(view('payment_method'), '付款方式');
});

Route::get('/anti-fraud', function(){
    return CustomClass::viewWithTitle(view('anti_fraud'), '防止詐騙、165反詐騙');
});

Route::get('/media-reports', function(){
    return CustomClass::viewWithTitle(view('media_reports'), '媒體報導');
});


Route::post('/products/search', [ShopController::class, 'searchProducts'])->name('shop.products.search');

Route::get('/categories/{categoryId}/products', [ShopController::class, 'indexCategoryProducts'])->name('shop.categories_products.index');

Route::get('/echo/{orderId}', [ShopController::class, 'echoT'])->name('echoT');
