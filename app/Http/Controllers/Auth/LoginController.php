<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Tenancy\Tenant;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /** Use a single input for both email or mobile */
    public function username()
    {
        return 'login';
    }

    /** Validate the unified login field */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login'    => ['required','string','max:255'],
            'password' => ['required','string'],
        ]);
    }

    /**
     * Build credentials for email OR phone, and add org_id when on a tenant.
     * Eloquent user provider will use any non-password keys we return here.
     */
    protected function credentials(Request $request)
    {
        $login = trim((string) $request->input($this->username()));
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // Normalize phone: keep digits; keep leading "+" if present
        $normalizedPhone = preg_replace('/(?!^\+)\D+/', '', $login);

        $creds = [
            'password' => $request->input('password'),
        ];

        if ($orgId = Tenant::id()) {
            // tenant-scoped login
            $creds['org_id'] = $orgId;
        }
        // On central, don't add org_id — your authenticated() gates by role.

        if ($isEmail) {
            $creds['email'] = $login;
        } else {
            $creds['phone'] = $normalizedPhone;
        }
        return $creds;
    }

    /** After success, enforce central vs tenant rules */
    // protected function authenticated(Request $request, $user)
    // {
    //     $isCentral = Tenant::id() === null;

    //     if ($isCentral && !$user->hasRole(['super admin'])) {
    //         Auth::logout();
    //         return back()->withErrors([
    //             $this->username() => 'Please use your organization subdomain to sign in.',
    //         ])->withInput($request->only($this->username()));
    //     }
    // }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed') . ' (wrong organization or credentials)'],
        ]);
    }

    /** Role-based redirect (unchanged) */
    public function redirectTo()
    {
        $user = Auth::user();
        if (!$user) return '/';

        // if ($user->hasRole('super admin'))  return 'su/dashboard';
        // if ($user->hasRole('org admin'))    return 'org/dashboard';
        // if ($user->hasRole('data_entry'))    return 'org/dashboard';
        // if ($user->hasRole('employee'))     return 'employee/dashboard';
        // if ($user->hasRole('channel partner')) return 'cp/dashboard';
        if($user->hasRole('super admin'))
        {
            return 'su/dashboard';
        }
        else if($user->hasRole('org admin'))
        {
            return 'su/dashboard';
        }
        else if($user->hasRole('data entry'))
        {

            return 'org/dashboard';
        }
        else if($user->hasRole('employee'))
        {
            return 'employee/dashboard';
        }
        else if($user->hasRole('channel partner'))
        {
            return 'cp/dashboard';
        }
        else if($user->hasRole('property consultant'))
        {
            return 'consultant/dashboard';
        }
        else if($user->hasRole('freelancer'))
        {
            return 'freelancer/dashboard';
        }
        else if($user->hasRole('rm'))
        {
            return 'rm/dashboard';
        }




        return '/login';
    }


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function logout(Request $request)
    {
        Session::forget('company_id');
        Auth::logout();
        return redirect('/');
    }
}
