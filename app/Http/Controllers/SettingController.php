<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Mail\EmailTest;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Artisan;
use File;
use Illuminate\Support\Facades\Validator;
use App\Models\Webhook;
use Google\Service\ServiceControl\Auth;


class SettingController extends Controller
{

    public function index()
    {
        if (\Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $settings = Utility::settings();

            $payment = Utility::set_payment_settings();
            $webhooks = Webhook::where('created_by', \Auth::user()->id)->get();

            return view('settings.index', compact('settings', 'payment', 'webhooks'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveBusinessSettings(Request $request)
    {
        $user = \Auth::user();

        $setting_field = Utility::setting_field($request->all());

        if (\Auth::user()->type == 'super admin') {

        }
        if ($user->type == 'super admin') {
            if ($request->logo_dark) {
                $request->validate(
                    [
                        'logo_dark' => 'image',
                    ]
                );
                $logoName = 'logo-dark.png';
                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];
                $path = Utility::upload_file($request, 'logo_dark', $logoName, $dir, $validation);
                if ($path['flag'] == 1) {
                    $logo_dark = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'logo_dark',
                        $user->creatorId(),
                    ]
                );
            }
            if ($request->logo_light) {
                $request->validate(
                    [
                        'logo_light' => 'image',
                    ]
                );
                $logoName = 'logo-light.png';
                // $path     = $request->file('logo_light')->storeAs('uploads/logo/', $logoName);
                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $path = Utility::upload_file($request, 'logo_light', $logoName, $dir, $validation);
                if ($path['flag'] == 1) {
                    $logo_light = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'logo_light',
                        $user->creatorId(),
                    ]
                );
            }
            if ($request->favicon) {
                $request->validate(
                    [
                        'favicon' => 'image',
                    ]
                );
                $favicon = 'favicon.png';
                // $path    = $request->file('favicon')->storeAs('uploads/logo/', $favicon);
                $dir = 'uploads/logo/';

                $validation = [
                    'mimes:' . 'png',
                    'max:' . '20480',
                ];

                $path = Utility::upload_file($request, 'favicon', $favicon, $dir, $validation);
                if ($path['flag'] == 1) {
                    $favicon = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $favicon,
                        'favicon',
                        $user->creatorId(),
                    ]
                );
            }

            if (!empty($request->title_text) || !empty($request->footer_text) || !empty($request->default_language) || !empty($request->display_landing_page) || !empty($request->gdpr_cookie) || !empty($request->color) || !empty($request->cust_theme_bg) || !empty($request->cust_darklayout)) {

                $post = $request->all();
                if (!isset($request->display_landing_page)) {
                    $post['display_landing_page'] = 'off';
                }
                if (!isset($request->gdpr_cookie)) {
                    $post['gdpr_cookie'] = 'off';
                }
                if (!isset($request->signup_button)) {
                    $post['signup_button'] = 'off';
                }
                if (!isset($request->cust_theme_bg)) {
                    $post['cust_theme_bg'] = 'off';
                }
                if (!isset($request->cust_darklayout)) {
                    $post['cust_darklayout'] = 'off';
                }
                if(isset($request->color) && $request->color_flag == 'false')
                {
                    $post['color'] = $request->color;
                }
                else
                {
                    $post['color'] = $request->custom_color;
                }
                if (!isset($request->verified_button)) {
                    $post['verified_button'] = 'off';
                }
                $SITE_RTL = $request->has('SITE_RTL') ? $request->SITE_RTL : 'off';
                $post['SITE_RTL'] = $SITE_RTL;

                unset($post['_token'], $post['logo_dark'], $post['logo_light'], $post['favicon']);
                foreach ($post as $key => $data) {
                    \DB::insert(
                        'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                        [
                            $data,
                            $key,
                            $user->creatorId(),
                        ]
                    );
                }
            }
        } else if (\Auth::user()->type == 'owner') {

            if ($request->company_logo_dark) {
                $request->validate(
                    [
                        'company_logo_dark' => 'image',
                    ]
                );
                $logoName     = $user->id . '_logo-dark.png';
                $image_size = $request->file('company_logo_dark')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {
                    $dir = 'uploads/logo/';

                    $validation = [
                        'mimes:' . 'png',
                        'max:' . '20480',
                    ];
                    $path = Utility::upload_file($request, 'company_logo_dark', $logoName, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_logo_dark = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
                // $path         = $request->file('company_logo_dark')->storeAs('uploads/logo/', $logoName);

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'company_logo_dark',
                        \Auth::user()->creatorId(),
                    ]
                );
            }
            if ($request->company_logo_light) {
                $request->validate(
                    [
                        'company_logo_light' => 'image',
                    ]
                );
                $logoName     = $user->id . '_logo-light.png';
                $image_size = $request->file('company_logo_light')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    $dir = 'uploads/logo/';

                    $validation = [
                        'mimes:' . 'png',
                        'max:' . '20480',
                    ];
                    $path = Utility::upload_file($request, 'company_logo_light', $logoName, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_logo_light = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
                // $path         = $request->file('company_logo_light')->storeAs('uploads/logo/', $logoName);

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $logoName,
                        'company_logo_light',
                        \Auth::user()->creatorId(),
                    ]
                );
            }
            if ($request->company_favicon) {
                $request->validate(
                    [
                        'company_favicon' => 'image',
                    ]
                );
                $favicon = $user->id . '_favicon.png';
                // $path    = $request->file('company_favicon')->storeAs('uploads/logo/', $favicon);
                $image_size = $request->file('company_favicon')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if ($result == 1) {
                    $dir = 'uploads/logo/';

                    $validation = [
                        'mimes:' . 'png',
                        'max:' . '20480',
                    ];
                    $path = Utility::upload_file($request, 'company_favicon', $favicon, $dir, $validation);

                    if ($path['flag'] == 1) {
                        $company_favicon = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $favicon,
                        'company_favicon',
                        \Auth::user()->creatorId(),
                    ]
                );
            }
            if (!empty($request->title_text) || !empty($request->footer_text) || !empty($request->default_owner_language) || !empty($request->display_landing_page) || !empty($request->gdpr_cookie) || !empty($request->color) || !empty($request->cust_theme_bg) || !empty($request->cust_darklayout) || !empty($request->SITE_RTL)) {

                $post = $request->all();
                if (!isset($request->display_landing_page)) {
                    $post['display_landing_page'] = 'off';
                }
                if (!isset($request->gdpr_cookie)) {
                    $post['gdpr_cookie'] = 'off';
                }
                if (!isset($request->signup_button)) {
                    $post['signup_button'] = 'off';
                }
                if (!isset($request->cust_theme_bg)) {
                    $post['cust_theme_bg'] = 'off';
                }
                if (!isset($request->cust_darklayout)) {
                    $post['cust_darklayout'] = 'off';
                }
                if(isset($request->color) && $request->color_flag == 'false')
                {
                    $post['color'] = $request->color;
                }
                else
                {
                    $post['color'] = $request->custom_color;
                }
                $SITE_RTL = $request->has('SITE_RTL') ? $request->SITE_RTL : 'off';
                $post['SITE_RTL'] = $SITE_RTL;

                unset($post['_token'], $post['company_logo_light'], $post['company_logo_dark'], $post['company_favicon']);
                foreach ($post as $key => $data) {
                    \DB::insert(
                        'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                        [
                            $data,
                            $key,
                            $user->creatorId(),
                        ]
                    );
                }
            }
            // if (!empty($request->title_text)) {
            //     $post = $request->all();
            //     unset($post['_token'], $post['company_logo_light'], $post['company_logo_dark'], $post['company_favicon']);
            //     foreach ($post as $key => $data) {
            //         \DB::insert(
            //             'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
            //             [
            //                 $data,
            //                 $key,
            //                 \Auth::user()->creatorId(),
            //             ]
            //         );
            //     }
            // }
        } else {

            return redirect()->back()->with('error', __('Permission denied.'));
        }
        return redirect()->back()->with('success', 'Business setting succefully saved.' . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
    }

    public function saveCompanySettings(Request $request)
    {

        if (\Auth::user()->type == 'owner') {
            $setting_field = Utility::setting_field($request->all());

            // if (!$setting_field) {
            //     return redirect()->back()->with('error', __('Permission denied.'));
            // }

            $request->validate(
                [
                    'company_name' => 'required|string|max:50',
                    // 'company_email' => 'required',
                    // 'company_email_from_name' => 'required|string',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                    ]
                );
            }

            return redirect()->back()->with('success', __('Company Setting successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveEmailSettings(Request $request)
    {
        if (\Auth::user()->type == 'super admin') {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];
            $validator = \Validator::make(
                $request->all(),
                $rules
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $post = $request->all();
            unset($post['_token']);

            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                    ]
                );
            }

            return redirect()->back()->with('success', __('Email Setting successfully updated.'));
        } elseif (\Auth::user()->type == 'owner') {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];
            $validator = \Validator::make(
                $request->all(),
                $rules
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $post = $request->all();
            unset($post['_token']);

            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                    ]
                );
            }
            return redirect()->back()->with('success', __('Email Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        Artisan::call('config:cache');
        Artisan::call('config:clear');
    }

    public function saveSystemSettings(Request $request)
    {
        $setting_field = Utility::setting_field($request->all());

        if (!$setting_field) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if (\Auth::user()->type == 'owner') {
            $request->validate(
                [
                    'site_currency' => 'required',
                ]
            );
            $post = $request->all();
            unset($post['_token']);
            if (!isset($post['shipping_display'])) {
                $post['shipping_display'] = 'off';
            }
            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }

            return redirect()->back()->with('success', __('System Setting successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function savePusherSettings(Request $request)
    {

        if (\Auth::user()->type == 'super admin') {
            $request->validate(
                [
                    'pusher_app_id' => 'required',
                    'pusher_app_key' => 'required',
                    'pusher_app_secret' => 'required',
                    'pusher_app_cluster' => 'required',
                ]
            );


            $post['pusher_app_id']      = $request->pusher_app_id;
            $post['pusher_app_key']     = $request->pusher_app_key;
            $post['pusher_app_secret']  = $request->pusher_app_secret;
            $post['pusher_app_cluster'] = $request->pusher_app_cluster;

            foreach ($post as $key => $data) {

                $arr = [
                    $data,
                    $key,
                    \Auth::user()->id,
                ];

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    $arr
                );
            }

            return redirect()->back()->with('success', 'Pusher setting successfully updated.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
//d
    public function testMail(Request $request)
    {
        $user = \Auth::user();
        if ($user->type == 'super admin' || $user->type == 'owner') {
            $data                      = [];
            $data['mail_driver']       = $request->mail_driver;
            $data['mail_host']         = $request->mail_host;
            $data['mail_port']         = $request->mail_port;
            $data['mail_username']     = $request->mail_username;
            $data['mail_password']     = $request->mail_password;
            $data['mail_encryption']   = $request->mail_encryption;
            $data['mail_from_address'] = $request->mail_from_address;
            $data['mail_from_name']    = $request->mail_from_name;
            return view('settings.test_mail', compact('data'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
        // return view('settings.test_mail');
    }

    public function testSendMail(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'mail_driver' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_from_address' => 'required',
                'mail_from_name' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(
                [
                    'is_success' => false,
                    'message' => $messages->first(),
                ]
            );
        }

        try {
            config(
                [
                    'mail.driver' => $request->mail_driver,
                    'mail.host' => $request->mail_host,
                    'mail.port' => $request->mail_port,
                    'mail.encryption' => $request->mail_encryption,
                    'mail.username' => $request->mail_username,
                    'mail.password' => $request->mail_password,
                    'mail.from.address' => $request->mail_from_address,
                    'mail.from.name' => $request->mail_from_name,
                ]
            );

            Mail::to($request->email)->send(new EmailTest());
        } catch (\Exception $e) {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return response()->json(
            [
                'is_success' => true,
                'message' => __('Email send Successfully'),
            ]
        );
    }

    public function savePaymentSettings(Request $request)
    {
        $user = \Auth::user();
        $validator = \Validator::make(
            $request->all(),
            [
                'currency' => 'required|string|max:255',
                'currency_symbol' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        } else {
            if ($user->type == 'Super Admin') {
                $arrEnv['CURRENCY_SYMBOL'] = $request->currency_symbol;
                $arrEnv['CURRENCY'] = $request->currency;
                $env = Utility::setEnvironmentValue($arrEnv);
            }

            $post['currency_symbol'] = $request->currency_symbol;
            $post['currency'] = $request->currency;
        }

        if (isset($request->is_manually_enabled) && $request->is_manually_enabled == 'on') {
            $post['is_manually_enabled'] = $request->is_manually_enabled;
        } else {
            $post['is_manually_enabled'] = 'off';
        }

        if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {
            $post['is_bank_enabled'] = $request->is_bank_enabled;
            $post['bank_details']   = $request->bank_details;
        } else {
            $post['is_bank_enabled'] = 'off';
        }
        if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'stripe_key' => 'required|string',
            //         'stripe_secret' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_stripe_enabled']     = $request->is_stripe_enabled;
            $post['stripe_secret']         = $request->stripe_secret;
            $post['stripe_key']            = $request->stripe_key;
        } else {
            $post['is_stripe_enabled'] = 'off';
        }


        if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'paypal_mode' => 'required|string',
            //         'paypal_client_id' => 'required|string',
            //         'paypal_secret_key' => 'required|string',
            //     ]
            // );
            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_paypal_enabled'] = $request->is_paypal_enabled;
            $post['paypal_mode']       = $request->paypal_mode;
            $post['paypal_client_id']  = $request->paypal_client_id;
            $post['paypal_secret_key'] = $request->paypal_secret_key;
        } else {
            $post['is_paypal_enabled'] = 'off';
        }

        if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'paystack_public_key' => 'required|string',
            //         'paystack_secret_key' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_paystack_enabled'] = $request->is_paystack_enabled;
            $post['paystack_public_key'] = $request->paystack_public_key;
            $post['paystack_secret_key'] = $request->paystack_secret_key;
        } else {
            $post['is_paystack_enabled'] = 'off';
        }


        if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'paymentwall_public_key' => 'required|string',
            //         'paymentwall_private_key' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
            $post['paymentwall_public_key'] = $request->paymentwall_public_key;
            $post['paymentwall_private_key'] = $request->paymentwall_private_key;
        } else {
            $post['is_paymentwall_enabled'] = 'off';
        }


        if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'flutterwave_public_key' => 'required|string',
            //         'flutterwave_secret_key' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
            $post['flutterwave_public_key'] = $request->flutterwave_public_key;
            $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
        } else {
            $post['is_flutterwave_enabled'] = 'off';
        }

        if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'razorpay_public_key' => 'required|string',
            //         'razorpay_secret_key' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
            $post['razorpay_public_key'] = $request->razorpay_public_key;
            $post['razorpay_secret_key'] = $request->razorpay_secret_key;
        } else {
            $post['is_razorpay_enabled'] = 'off';
        }

        if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
            $request->validate(
                [
                    'mercado_access_token' => 'required|string',
                ]
            );
            $post['is_mercado_enabled'] = $request->is_mercado_enabled;
            $post['mercado_access_token']     = $request->mercado_access_token;
            $post['mercado_mode'] = $request->mercado_mode;
        } else {
            $post['is_mercado_enabled'] = 'off';
        }

        if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {

            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'paytm_mode' => 'required',
            //         'paytm_merchant_id' => 'required|string',
            //         'paytm_merchant_key' => 'required|string',
            //         'paytm_industry_type' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_paytm_enabled']    = $request->is_paytm_enabled;
            $post['paytm_mode']          = $request->paytm_mode;
            $post['paytm_merchant_id']   = $request->paytm_merchant_id;
            $post['paytm_merchant_key']  = $request->paytm_merchant_key;
            $post['paytm_industry_type'] = $request->paytm_industry_type;
        } else {
            $post['is_paytm_enabled'] = 'off';
        }

        if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {


            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'mollie_api_key' => 'required|string',
            //         'mollie_profile_id' => 'required|string',
            //         'mollie_partner_id' => 'required',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_mollie_enabled'] = $request->is_mollie_enabled;
            $post['mollie_api_key']    = $request->mollie_api_key;
            $post['mollie_profile_id'] = $request->mollie_profile_id;
            $post['mollie_partner_id'] = $request->mollie_partner_id;
        } else {
            $post['is_mollie_enabled'] = 'off';
        }

        if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {



            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'skrill_email' => 'required|email',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_skrill_enabled'] = $request->is_skrill_enabled;
            $post['skrill_email']      = $request->skrill_email;
        } else {
            $post['is_skrill_enabled'] = 'off';
        }

        if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {


            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_coingate_enabled'] = $request->is_coingate_enabled;
            $post['coingate_mode']       = $request->coingate_mode;
            $post['coingate_auth_token'] = $request->coingate_auth_token;
        } else {
            $post['is_coingate_enabled'] = 'off';
        }
        if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {


            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_toyyibpay_enabled'] = $request->is_toyyibpay_enabled;
            $post['toyyibpay_secret_key'] = $request->toyyibpay_secret_key;
            $post['category_code']       = $request->category_code;
        } else {
            $post['is_toyyibpay_enabled'] = 'off';
        }
        if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_payfast_enabled'] = $request->is_payfast_enabled;
            $post['payfast_merchant_id'] = $request->payfast_merchant_id;
            $post['payfast_merchant_key']       = $request->payfast_merchant_key;
            $post['payfast_signature'] = $request->payfast_signature;
            $post['payfast_mode'] = $request->payfast_mode;
        } else {
            $post['is_payfast_enabled'] = 'off';
        }
        if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_iyzipay_enabled'] = $request->is_iyzipay_enabled;
            $post['iyzipay_mode'] = $request->iyzipay_mode;
            $post['iyzipay_key']       = $request->iyzipay_key;
            $post['iyzipay_secret'] = $request->iyzipay_secret;
        } else {
            $post['is_iyzipay_enabled'] = 'off';
        }
        if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_sspay_enabled'] = $request->is_sspay_enabled;
            $post['sspay_secret_key'] = $request->sspay_secret_key;
            $post['sspay_category_code']       = $request->sspay_category_code;
        } else {
            $post['is_sspay_enabled'] = 'off';
        }
        if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_paytab_enabled'] = $request->is_paytab_enabled;
            $post['paytab_profile_id'] = $request->paytab_profile_id;
            $post['paytab_server_key'] = $request->paytab_server_key;
            $post['paytab_region'] = $request->paytab_region;
        } else {
            $post['is_paytab_enabled'] = 'off';
        }
        if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_benefit_enabled'] = $request->is_benefit_enabled;
            $post['benefit_api_key'] = $request->benefit_api_key;
            $post['benefit_secret_key'] = $request->benefit_secret_key;
        } else {
            $post['is_benefit_enabled'] = 'off';
        }
        if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_cashfree_enabled'] = $request->is_cashfree_enabled;
            $post['cashfree_api_key'] = $request->cashfree_api_key;
            $post['cashfree_secret_key'] = $request->cashfree_secret_key;
        } else {
            $post['is_cashfree_enabled'] = 'off';
        }
        if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
            // $validator = \Validator::make(
            //     $request->all(),
            //     [
            //         'coingate_mode' => 'required|string',
            //         'coingate_auth_token' => 'required|string',
            //     ]
            // );

            // if ($validator->fails()) {
            //     $messages = $validator->getMessageBag();

            //     return redirect()->back()->with('error', $messages->first());
            // }

            $post['is_aamarpay_enabled'] = $request->is_aamarpay_enabled;
            $post['aamarpay_store_id'] = $request->aamarpay_store_id;
            $post['aamarpay_signature_key'] = $request->aamarpay_signature_key;
            $post['aamarpay_description'] = $request->aamarpay_description;
        } else {
            $post['is_aamarpay_enabled'] = 'off';
        }

        if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {


            $post['is_paytr_enabled'] = $request->is_paytr_enabled;
            $post['paytr_merchant_id'] = $request->paytr_merchant_id;
            $post['paytr_merchant_key'] = $request->paytr_merchant_key;
            $post['paytr_merchant_salt'] = $request->paytr_merchant_salt;
        } else {
            $post['is_paytr_enabled'] = 'off';
        }

        if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {

            $post['is_yookassa_enabled'] = $request->is_yookassa_enabled;
            $post['yookassa_shop_id'] = $request->yookassa_shop_id;
            $post['yookassa_secret'] = $request->yookassa_secret;
        } else {
            $post['is_yookassa_enabled'] = 'off';
        }

        if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
            $post['is_midtrans_enabled'] = $request->is_midtrans_enabled;
            $post['midtrans_mode'] = $request->midtrans_mode;
            $post['midtrans_secret'] = $request->midtrans_secret;
        } else {
            $post['is_midtrans_enabled'] = 'off';
        }

        if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {

            $post['is_xendit_enabled'] = $request->is_xendit_enabled;
            $post['xendit_token'] = $request->xendit_token;
            $post['xendit_api'] = $request->xendit_api;
        } else {
            $post['is_xendit_enabled'] = 'off';
        }

        if (isset($request->is_nepalste_enabled ) && $request->is_nepalste_enabled == 'on') {

            $post['is_nepalste_enabled'] = $request->is_nepalste_enabled;
            $post['nepalste_mode']      = $request->nepalste_mode;
            $post['nepalste_public_key'] = $request->nepalste_public_key;
            $post['nepalste_secret_key'] = $request->nepalste_secret_key;
        } else {
            $post['is_nepalste_enabled'] = 'off';
        }

        if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
            $request->validate(
                [
                    'paiementpro_merchant_id' => 'required|string',
                ]
            );

            $post['is_paiementpro_enabled']      = $request->is_paiementpro_enabled;
            $post['paiementpro_merchant_id']     = $request->paiementpro_merchant_id;
        } else {
            $post['is_paiementpro_enabled'] = 'off';
        }

         if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {

            $request->validate(
                [
                    'fedapay_mode' => 'required',
                    'fedapay_public_key' => 'required',
                    'fedapay_secret_key' => 'required',
                ]
            );
            $post['is_fedapay_enabled'] = $request->is_fedapay_enabled;
            $post['fedapay_mode']       = $request->fedapay_mode;
            $post['fedapay_public_key']  = $request->fedapay_public_key;
            $post['fedapay_secret_key'] = $request->fedapay_secret_key;
        } else {
            $post['is_fedapay_enabled'] = 'off';
        }


        if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {

            $request->validate(
                [
                    'payhere_mode' => 'required',
                    'payhere_merchant_id' => 'required',
                    'payhere_merchant_secret' => 'required',
                    'payhere_app_id' => 'required',
                    'payhere_app_secret' => 'required',
                ]
            );
            $post['is_payhere_enabled'] = $request->is_payhere_enabled;
            $post['payhere_mode']       = $request->payhere_mode;
            $post['payhere_merchant_id']  = $request->payhere_merchant_id;
            $post['payhere_merchant_secret'] = $request->payhere_merchant_secret;
            $post['payhere_app_id'] = $request->payhere_app_id;
            $post['payhere_app_secret'] = $request->payhere_app_secret;
        } else {
            $post['is_payhere_enabled'] = 'off';
        }

        if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {

            $request->validate(
                [
                    'cinetpay_api_key' => 'required',
                    'cinetpay_site_id' => 'required',
                ]
            );
            $post['is_cinetpay_enabled'] = $request->is_cinetpay_enabled;
            $post['cinetpay_api_key']  = $request->cinetpay_api_key;
            $post['cinetpay_site_id'] = $request->cinetpay_site_id;
        } else {
            $post['is_cinetpay_enabled'] = 'off';
        }


        foreach ($post as $key => $data) {
            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            $insert_payment_setting = \DB::insert(
                'insert into admin_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        // Artisan::call('config:cache');
        // Artisan::call('config:clear');

        return redirect()->back()->with('success', __('Payment Settings updated successfully.'));
    }

    public function twilio(Request $request)
    {
        // return redirect()->back()->with('error', __('This operation is not perform due to demo mode.'));
        $user = \Auth::user();
        $post = [];
        $post['twilio_sid'] = $request->input('twilio_sid');
        $post['twilio_token'] = $request->input('twilio_token');
        $post['twilio_from'] = $request->input('twilio_from');

        $post['twilio_user_create'] = $request->has('twilio_user_create') ? $request->input('twilio_user_create') : 0;

        $post['twilio_lead_create'] = $request->has('twilio_lead_create') ? $request->input('twilio_lead_create') : 0;

        $post['twilio_quotes_create'] = $request->has('twilio_quotes_create') ? $request->input('twilio_quotes_create') : 0;

        $post['twilio_salesorder_create'] = $request->has('twilio_salesorder_create') ? $request->input('twilio_salesorder_create') : 0;

        $post['twilio_invoice_create'] = $request->has('twilio_invoice_create') ? $request->input('twilio_invoice_create') : 0;


        $post['twilio_invoicepay_create'] = $request->has('twilio_invoicepay_create') ? $request->input('twilio_invoicepay_create') : 0;

        $post['twilio_meeting_create'] = $request->has('twilio_meeting_create') ? $request->input('twilio_meeting_create') : 0;


        $post['twilio_task_create'] = $request->has('twilio_task_create') ? $request->input('twilio_task_create') : 0;

        if (isset($post) && !empty($post) && count($post) > 0) {
            $created_at = $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                    [
                        $data,
                        $key,
                        $user->id,
                        $created_at,
                        $updated_at,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('Twillio Settings updated successfully.'));
    }

    public function recaptchaSettingStore(Request $request)
    {
        if (\Auth::user()->type == 'super admin') {

            $request->validate(
                [
                    'google_recaptcha_version' => 'required',
                    'google_recaptcha_key'    => 'required',
                    'google_recaptcha_secret' => 'required',
                ]
            );
            $post['recaptcha_module']        = $request->recaptcha_module ?? 'on';
            $post['google_recaptcha_key']    = $request->google_recaptcha_key;
            $post['google_recaptcha_secret'] = $request->google_recaptcha_secret;
            $post['google_recaptcha_version']  = $request->google_recaptcha_version;

            foreach ($post as $key => $data) {

                $arr = [
                    $data,
                    $key,
                    \Auth::user()->id,
                ];

                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    $arr
                );
            }

            return redirect()->back()->with('success', 'Recaptcha Setting successfully updated.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storageSettingStore(Request $request)
    {

        if (isset($request->storage_setting) && $request->storage_setting == 'local') {

            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 's3') {
            $request->validate(
                [
                    's3_key'                  => 'required',
                    's3_secret'               => 'required',
                    's3_region'               => 'required',
                    's3_bucket'               => 'required',
                    's3_url'                  => 'required',
                    's3_endpoint'             => 'required',
                    's3_max_upload_size'      => 'required',
                    's3_storage_validation'   => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['s3_key']                     = $request->s3_key;
            $post['s3_secret']                  = $request->s3_secret;
            $post['s3_region']                  = $request->s3_region;
            $post['s3_bucket']                  = $request->s3_bucket;
            $post['s3_url']                     = $request->s3_url;
            $post['s3_endpoint']                = $request->s3_endpoint;
            $post['s3_max_upload_size']         = $request->s3_max_upload_size;
            $s3_storage_validation              = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation']      = $s3_storage_validation;
        }
        if (isset($request->storage_setting) && $request->storage_setting == 'wasabi') {
            $request->validate(
                [
                    'wasabi_key'                    => 'required',
                    'wasabi_secret'                 => 'required',
                    'wasabi_region'                 => 'required',
                    'wasabi_bucket'                 => 'required',
                    'wasabi_url'                    => 'required',
                    'wasabi_root'                   => 'required',
                    'wasabi_max_upload_size'        => 'required',
                    'wasabi_storage_validation'     => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['wasabi_key']                 = $request->wasabi_key;
            $post['wasabi_secret']              = $request->wasabi_secret;
            $post['wasabi_region']              = $request->wasabi_region;
            $post['wasabi_bucket']              = $request->wasabi_bucket;
            $post['wasabi_url']                 = $request->wasabi_url;
            $post['wasabi_root']                = $request->wasabi_root;
            $post['wasabi_max_upload_size']     = $request->wasabi_max_upload_size;
            $wasabi_storage_validation          = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation']  = $wasabi_storage_validation;
        }

        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function saveGoogleCalenderSettings(Request $request)
    {
        if (isset($request->is_enabled) && $request->is_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'google_clender_id' => 'required',
                    'google_calender_json_file' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_enabled'] = $request->is_enabled;
        } else {
            $post['is_enabled'] = 'off';
        }


        if ($request->google_calender_json_file) {
            $dir       = storage_path() . '/' . md5(time());
            if (!is_dir($dir)) {
                File::makeDirectory($dir, $mode = 0777, true, true);
            }
            $file_name = $request->google_calender_json_file->getClientOriginalName();
            $file_path =  md5(time()) . "/" . md5(time()) . "." . $request->google_calender_json_file->getClientOriginalExtension();

            $file = $request->file('google_calender_json_file');
            $file->move($dir, $file_path);
            $post['google_calender_json_file']            = $file_path;
        }
        if ($request->google_clender_id) {
            $post['google_clender_id']            = $request->google_clender_id;
            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->id,
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }
        return redirect()->back()->with('success', 'Google Calendar setting successfully updated.');
    }

    public function saveSEOSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'meta_keywords' => 'required',
                'meta_description' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        if ($request->meta_image) {
            $img_name = time() . '_' . 'meta_image.png';
            $dir = 'uploads/metaevent/';
            $validation = [
                'max:' . '20480',
            ];
            $path = Utility::upload_file($request, 'meta_image', $img_name, $dir, $validation);
            if ($path['flag'] == 1) {
                $logo_dark = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['meta_image']  = $img_name;
        }
        $post['meta_keywords']            = $request->meta_keywords;
        $post['meta_description']            = $request->meta_description;
        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->id,
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s'),
                ]
            );
        }
        return redirect()->back()->with('success', 'SEO Setting successfully updated.');
    }
    public function saveCookieSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_description' => 'required',
                'contactus_url' => 'required',
            ]
        );
        $post = $request->all();

        unset($post['_token']);

        if ($request->enable_cookie) {
            $post['enable_cookie'] = 'on';
        } else {
            $post['enable_cookie'] = 'off';
        }
        if ($request->cookie_logging) {
            $post['cookie_logging'] = 'on';
        } else {
            $post['cookie_logging'] = 'off';
        }

        $post['cookie_title']            = $request->cookie_title;
        $post['cookie_description']            = $request->cookie_description;
        $post['strictly_cookie_title']            = $request->strictly_cookie_title;
        $post['strictly_cookie_description']            = $request->strictly_cookie_description;
        $post['more_information_description']            = $request->more_information_description;
        $post['contactus_url']            = $request->contactus_url;

        $settings = Utility::settings();
        foreach ($post as $key => $data) {


            if (in_array($key, array_keys($settings))) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->creatorId(),
                        date('Y-m-d H:i:s'),
                        date('Y-m-d H:i:s'),
                    ]
                );
            }
        }
        return redirect()->back()->with('success', 'Cookie setting successfully saved.');
    }
    public function CookieConsent(Request $request)
    {
        $settings = Utility::settings();
        if ($request['cookie']) {
            if ($settings['enable_cookie'] == "on" && $settings['cookie_logging'] == "on") {
                $allowed_levels = ['necessary', 'analytics', 'targeting'];
                $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                    return in_array($level, $allowed_levels);
                });

                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                // Generate new CSV line
                $browser_name = $whichbrowser->browser->name ?? null;
                $os_name = $whichbrowser->os->name ?? null;
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
                // $ip = '49.36.83.154';
                $ip = $_SERVER['REMOTE_ADDR']; // your ip address here

                $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));


                $date = (new \DateTime())->format('Y-m-d');
                $time = (new \DateTime())->format('H:i:s') . ' UTC';

                $new_line = implode(',', [$ip, $date, $time, json_encode($request['cookie']), $device_type, $browser_language, $browser_name, $os_name, isset($query) ? $query['country'] : '', isset($query) ? $query['region'] : '', isset($query) ? $query['regionName'] : '', isset($query) ? $query['city'] : '', isset($query) ? $query['zip'] : '', isset($query) ? $query['lat'] : '', isset($query) ? $query['lon'] : '']);
                if (!file_exists(storage_path() . '/uploads/sample/data.csv')) {

                    $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name';
                    file_put_contents(storage_path() . '/uploads/sample/data.csv', $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
                }
                file_put_contents(storage_path() . '/uploads/sample/data.csv', $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);

                return response()->json('success');
            }
            return response()->json('error');
        } else {
            return redirect()->back();
        }
    }

    public function chatgptkey(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'chatgpt_key' => 'required',
                'chatgpt_model' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        if (\Auth::user()->type == 'super admin') {
            $user = \Auth::user();
            if (!empty($request->chatgpt_key)) {
                $post = $request->all();
                $post['chatgpt_key'] = $request->chatgpt_key;
                $post['chatgpt_model'] = $request->chatgpt_model;

                unset($post['_token']);
                foreach ($post as $key => $data) {
                    $settings = Utility::settings();
                    if (in_array($key, array_keys($settings))) {
                        \DB::insert(
                            'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                            [
                                $data,
                                $key,
                                $user->creatorId(),
                            ]
                        );
                    }
                }
            }
            return redirect()->back()->with('success', __('ChatGPT key successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
function get_device_type($user_agent)
{
    $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
    $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';

    if (preg_match_all($mobile_regex, $user_agent)) {
        return 'mobile';
    } else {

        if (preg_match_all($tablet_regex, $user_agent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }
}
