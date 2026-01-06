<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserService extends UserRepository
{
    public function register($request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($request->password);
        $input['role'] = '1';
        return UserRepository::create($input);
    }


    public function login($request, $credentials)
    {
        if (isset($request->remember)) {
            if (Auth::attempt($credentials, $request->remember)) {

                $request->session()->regenerate();
                return $this->switchRole();
            }
        } else {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return $this->switchRole();
            }
        }

        return back()->withErrors([
            'email' => '電子郵件或密碼錯誤',
        ]);
    }

    public function handleOauthRegisterOrLogin($oauthType, $oauthId, $name, $oauthEmail=null)
    {
        $userQuery = DB::table('users')->where([['oauth_type', '=', $oauthType], ['oauth_id', '=', $oauthId]]);
        if($userQuery->exists() !== true) {
            $input['oauth_type'] = $oauthType;
            $input['oauth_id'] = $oauthId;
            $input['name'] = $name;
            $input['role'] = '1';
            ($oauthEmail !== true ?$input['oauth_email']=$oauthEmail :false);

            return [UserRepository::create($input), 0];
        } else {
            $userId = $userQuery->first()->id;
            return [UserRepository::find($userId), 1];
        }
    }

    public function logout($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function switchRole()
    {
        $role = Auth::user()->role;
        $redirects = ['/admin/dashboard', '/account/dashboard'];
        return redirect($redirects[$role]);
    }

    public function updateAccount($request)
    {
        $input = $request->except(['_token']);
        UserRepository::fill($input, Auth::user()->id);
    }

    public function usePoint($usedPoint)
    {
        $userId = Auth::user()->id;
        $user = UserRepository::find($userId);
        $ownPoint =$user->point;
        $ownPoint = intval($ownPoint) - intval($usedPoint);
        $user->update(['point'=>$ownPoint]);
    }

    public function reductionPoint($userId, $reductionPoint)
    {
        $user = UserRepository::find($userId);
        $userPoint = $user->point;
        $userPoint = intval($userPoint) + intval($reductionPoint);
        $user->update(['point'=>$userPoint]);
    }

    public function ajaxGetAllUsers()
    {
        $users = User::with('orders')->get();

        $datatable = DataTables::collection($users)
            ->addColumn('id', function ($user)
            {
                return $user->id;
            })
            ->addColumn('name', function ($user)
            {
                return $user->name;
            })
            ->addColumn('email', function ($user)
            {
                return $user->email;
            })
            ->addColumn('oauth_type', function ($user)
            {
                if ($user->oauth_type == null)
                {
                    return '一般';
                } else {
                    return $user->oauth_type;
                }
            })
            ->addColumn('total', function ($user)
            {
                return 'NT$'.number_format($user->orders()->sum('total'));
            })
            ->toJson();

        return  $datatable;
    }
}
