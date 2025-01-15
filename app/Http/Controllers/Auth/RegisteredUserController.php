<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Events\VerifyReCaptchaToken;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        if (Utility::getValByName('signup_button') == 'on') {
            return view('auth.register');
        } else {
            return abort('404', 'Page not found');
        }
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validation = [];
        if ($request->has('terms_condition') && $request->terms_condition == 'off') {
            $validation['terms_condition_check'] = 'required';
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $settings = Utility::settings();
        if (isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes') {
            if ($settings['google_recaptcha_version'] == 'v2') {
                $validation['g-recaptcha-response'] = 'required';
            } elseif ($settings['google_recaptcha_version'] == 'v3') {
                $result = event(new VerifyReCaptchaToken($request));
                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]);
                    $validation['g-recaptcha-response'] = 'required';
                }
            }
        }
        $this->validate($request, $validation);


        do {
            $code = rand(100000, 999999);
        } while (User::where('referral_code', $code)->exists());

        $randomNumber = rand(100000, 999999);
        if (Utility::getValByName('verified_button') == "off") {
            $user = User::create([
                'username' => $request->name,
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => date('H:i:s'),
                'password' => Hash::make($request->password),
                'type' => 'owner',
                'lang' => 'en',
                'title' => '-',
                'avatar' => '',
                'plan' => Plan::first()->id,
                'created_by' => 1,
                'referral_code' => $randomNumber,
            ]);
            if (!empty($request->ref_id)) {
                $user->referral_user = $request->ref_id;
                $user->save();
            }

            $adminRole = Role::findByName('owner');

            $user->assignRole($adminRole);
            $user->userDefaultDataRegister($user->id);

            $uArr = [
                'email' => $user->email,
                'password' => $request->password,
            ];
            Auth::login($user);
            $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);

            if (isset($request->plan)) {
                try {
                    $plan = \Illuminate\Support\Facades\Crypt::decrypt($request->plan);

                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $plan = 0;

                }
            }
            if (isset($plan) && !empty($plan)) {
                return redirect()->route('plan.payment', \Illuminate\Support\Facades\Crypt::encrypt($plan));
            }

            return redirect(RouteServiceProvider::HOME);
        } else {
            $user = User::create([
                'username' => $request->name,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'owner',
                'eu' => 'en',
                'title' => '-',
                'avatar' => '',
                'plan' => Plan::first()->id,
                'created_by' => 1,
                'referral_code' => $randomNumber,

            ]);
            if (!empty($request->ref_id)) {
                $user->referral_user = $request->ref_id;
                $user->save();
            }
            if (empty($lang)) {
                $lang = Utility::getValByName('default_language');
            }
            \App::setLocale($lang);
            Utility::getSMTPDetails(1);

            try {
                event(new Registered($user));
                $role_r = Role::findByName('owner');
                $user->userDefaultDataRegister($user->id);
                $user->assignRole($role_r);
            } catch (\Exception $e) {

                $user->delete();
                return redirect('/register/lang?')->with('status', __('Email SMTP settings does not configure so please contact to your site admin.'));
            }
            Auth::login($user);

            return view('auth.verify-email', compact('lang'));
        }
    }


    public function showRegistrationForm(Request $request, $refId = '', $lang = '')
    {
        if (empty($lang)) {
            $lang = Utility::getValByName('default_language');
        }
        if ($refId == '') {
            $refId = 0;
        }
        $referralCode = User::where('referral_code', '=', $refId)->first();
        if ($referralCode) {
            if ($referralCode->referral_code != $refId) {
                return redirect()->route('register');
            }
        }
        \App::setLocale($lang);
        $plan = null;
        if ($request->plan) {
            $plan = $request->plan;
        }
        return view('auth.register', compact('lang', 'refId', 'plan'));
    }
}
