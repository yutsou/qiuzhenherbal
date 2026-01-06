<?php

namespace App\Http\Controllers\Auth;

use App\CustomFacades\CustomClass;
use App\Http\Controllers\Controller;
use App\Jobs\AssignBirthdayCoupon;
use App\Jobs\SendEmail;
use App\Models\User;
use App\Services\CartService;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\LineService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    private $lineService, $userService, $cartService, $couponService;

    public function __construct(LineService $lineService, UserService $userService, CartService $cartService, CouponService $couponService)
    {
        $this->lineService = $lineService;
        $this->userService = $userService;
        $this->cartService = $cartService;
        $this->couponService = $couponService;
    }

    protected function passwordRules()
    {
        return ['required', 'string', new Password, 'confirmed'];
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $rules = [
            'email' => 'required|unique:users|max:255',
            'password' => $this->passwordRules(),
        ];
        $messages = [
            'email.unique'=>'電子郵件已被使用過',
            'password.confirmed'=>'密碼不一致',
        ];
        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return redirect('/register')->withErrors($validator)->withInput();
        }

        $newUser = $this->userService->register($request);

        $this->couponService->createAndAssignCouponToNewUser($newUser);

        Auth::login($newUser);

        return redirect('/account/dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $redirect = $this->userService->login($request, $credentials);

        $this->cartService->guestCartToAuthCart();

        return $redirect;
    }

    public function redirectLineLogin()
    {
        $lineLoginUrl = $this->lineService->getLoginUrl();
        return redirect($lineLoginUrl);
    }

    public function lineCallback(Request $request)
    {
        if($request->state !== csrf_token())
        {
            return redirect('/warning');
        }
        $code = $request->code;
        $response = $this->lineService->getLineToken($code);
        $oauthUser = $this->lineService->getUserProfile($response['access_token']);

        $oauthType = 'line';
        $oauthId = $oauthUser['userId'];
        $name = $oauthUser['displayName'];

        $result = $this->userService->handleOauthRegisterOrLogin($oauthType, $oauthId, $name);

        if($result[1] === 0) {#新會員
            $this->couponService->createAndAssignCouponToNewUser($result[0]);
        }

        Auth::login($result[0]);

        return redirect('/account/dashboard');

    }

    public function redirectFacebookLogin()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback()
    {
        $oauthUser = Socialite::driver('facebook')->user();

        $oauthType = 'facebook';
        $oauthId = $oauthUser->id;
        $name = $oauthUser->name;
        (isset($oauthUser->email) === true ? $oauthEmail = $oauthUser->email : $oauthEmail = null);

        $result = $this->userService->handleOauthRegisterOrLogin($oauthType, $oauthId, $name, $oauthEmail);

        if($result[1] === 0) {#新會員
            $this->couponService->createAndAssignCouponToNewUser($result[0]);
        }

        Auth::login($result[0]);

        return redirect('/account/dashboard');
    }

    public function logout(Request $request)
    {
        $this->userService->logout($request);
        return redirect('/');
    }

    public function switchRole()
    {
        $redirect = $this->userService->switchRole();
        return $redirect;
    }

    public function editPassword()
    {
        return CustomClass::viewWithTitle(view('auth.edit'), '修改密碼');
    }

    public function updatePassword(Request $request)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $validator = Validator::make($request->all(), [
            'password' => $this->passwordRules(),
        ], ['password.confirmed'=>'密碼不一致']);

        $validator->after(function ($validator) use ($user, $request) {
            if (! isset($request->current_password) || ! Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', __('輸入的密碼與原有的密碼不相符'));
            }
        })->validate();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return back()->with('Success', '密碼修改成功');
    }

    public function showEmailVerificationRequest()
    {
        return CustomClass::viewWithTitle(view('auth.verify'), '驗證信箱');
    }

    public function sendEmailVerification()
    {
        $expire = Carbon::now()->addMinutes(30);

        $token = $expire.'userId'.Auth::user()->id;
        $signature = sha1($expire. $token);

        User::find(Auth::user()->id)->update(['confirm_token'=>$signature]);

        $url = route('account.email.verify').'?expire='.$expire.'&signature='.$signature.'&userId='.Auth::user()->id;

        $emailContent['type'] = 'emailVerification';
        $emailContent['emailAddress'] = Auth::user()->email;
        $emailContent['name'] = Auth::user()->name;
        $emailContent['url'] = $url;
        SendEmail::dispatch($emailContent);

        return redirect()->route('account.email_verification.request')->with('Success', '驗證信已發送至信箱');
    }

    public function verifyEmail(Request $request)
    {
        $now = Carbon::now();

        $expire = $request->expire;
        $signature = $request->signature;
        $userId = $request->userId;

        if(Carbon::createFromFormat('Y-m-d H:i:s', $expire)->gt($now)) {
            $token = $expire.'userId'.$userId;
            $confirm_signature = sha1($expire. $token);
            $user = User::find($userId);
            $confirm_token = $user->confirm_token;
            if($signature == $confirm_signature && $confirm_signature == $confirm_token) {
                if($user->email_verified_at === null) {
                    $user->update(['email_verified_at'=>$now]);
                    return redirect()->route('account.dashboard.show')->with('Success', '帳號驗證成功');
                } else {
                    return redirect()->route('account.dashboard.show')->with('Warning', '帳號已經驗證');
                }
            } else {
                return redirect()->route('account.email_verification.request')->with('Warning', '認證錯誤，請重新要求驗證信');
            }
        } else {
            return redirect()->route('account.email_verification.request')->with('Warning', '認證已經過期，請重新要求驗證信');
        }
    }

    public function forgotPassword()
    {
        return CustomClass::viewWithTitle(view('auth.forgot_password'), '忘記密碼');
    }

    public function sendResetPasswordConfirmation(Request $request)
    {
        $expire = Carbon::now()->addMinutes(30);

        $user = User::where('email', $request->email)->first();

        $token = $expire.'userId'.$user->id;
        $signature = sha1($expire. $token);

        $user->update(['confirm_token'=>$signature]);

        $url = route('password.reset').'?expire='.$expire.'&signature='.$signature.'&userId='.$user->id;

        $emailContent['type'] = 'passwordResetConfirmation';
        $emailContent['emailAddress'] = $user->email;
        $emailContent['name'] = $user->name;
        $emailContent['url'] = $url;
        SendEmail::dispatch($emailContent);

        return back();
    }

    public function resetPassword(Request $request)
    {
        $now = Carbon::now();

        $expire = $request->expire;
        $signature = $request->signature;
        $userId = $request->userId;

        if(Carbon::createFromFormat('Y-m-d H:i:s', $expire)->gt($now)) {
            $token = $expire.'userId'.$userId;
            $confirm_signature = sha1($expire. $token);
            $user = User::find($userId);
            $confirm_token = $user->confirm_token;
            if($signature == $confirm_signature && $confirm_signature == $confirm_token) {
                return CustomClass::viewWithTitle(view('auth.password_reset')->with('user', $user), '重置密碼');
            } else {
                return redirect()->route('password.forgot')->with('Warning', '認證錯誤，請重新要求重置信');
            }
        } else {
            return redirect()->route('password.forgot')->with('Warning', '認證已經過期，請重新要求重置信');
        }
    }

    public function setPassword(Request $request)
    {
        $userId = $request->userId;
        $user = User::find($userId);
        $validator = Validator::make($request->all(), [
            'password' => $this->passwordRules(),
        ], ['password.confirmed'=>'密碼不一致']);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('login')->with('Success', '密碼修改成功');
    }

}
