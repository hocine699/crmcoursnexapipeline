<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailTemplate;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Spatie\GoogleCalendar\Event;
use Illuminate\Support\Carbon;


class Utility extends Model
{

    private static $settings = null;
    private static $languages = null;


    public static function settings()
    {
        if (self::$settings === null) {
            self::$settings = self::fetchSettings();
        }

        return self::$settings;
    }

    public static function fetchSettings()
    {
        $data = DB::table('settings');

        if (\Auth::check()) {
            $data = $data->where('created_by', '=', \Auth::user()->creatorId())->get();
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
        } else {
            $data->where('created_by', '=', 1);
            $data = $data->get();
        }

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "invoice_prefix" => "#INV",
            "invoice_color" => "ffffff",
            "invoice_logo" => "",
            "quote_template" => "template1",
            "quote_color" => "ffffff",
            "quote_logo" => "",
            "salesorder_template" => "template1",
            "salesorder_color" => "ffffffcompany_name",
            "salesorder_logo" => "",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "fffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "fffff",
            "quote_prefix" => "#QUO",
            "salesorder_prefix" => "#SOP",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "VAT",
            "shipping_display" => "on",
            "footer_link_1" => "Support",
            "footer_value_1" => "#",
            "footer_link_2" => "Terms",
            "footer_value_2" => "#",
            "footer_link_3" => "Privacy",
            "footer_value_3" => "#",
            "display_landing_page" => "on",
            "company_favicon" => "",
            "cookie_text" => "",
            "signup_button" => "on",
            "color" => "theme-3",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "SITE_RTL" => "off",
            "contract_prefix" => "#CON",
            "contract_template" => "template1",
            "owner_signature" => "",
            "default_language" => "en",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "is_enabled" => "",
            "verified_button" => "on",
            "enable_cookie" => "",
            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'contactus_url' => '#',
            'chatgpt_key' => '',
            'disable_lang' => '',
            'meta_keywords' => 'Salesy SaaS',
            'meta_description' => 'Sales are indeed the thriving need of any organization. Salesly SaaS is a business sales CRM tool, here to make your complex sales activity a lot easier.',
            "meta_image" => "",
            "recaptcha_module" => 'no',
            "google_recaptcha_secret" => '',
            "google_recaptcha_key" => '',
            "pusher_app_key" => '',
            "pusher_app_secret" => '',
            "pusher_app_cluster" => '',
            "chatgpt_model" => '',
            "is_qr_enabled" => "on",
            "salesorder_qr_enabled" => "on",
            "quote_qr_enabled" => "on",
            'recaptcha_module'          => '',
            'google_recaptcha_version'  => '',
            'google_recaptcha_key'      => '',
            'google_recaptcha_secret'   => '',
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        config(
            [
                'captcha.secret' => $settings['google_recaptcha_secret'],
                'captcha.sitekey' => $settings['google_recaptcha_key'],
                'options' => [
                    'timeout' => 30,
                ],
            ]
        );
        return $settings;
    }

    public static function settingsById($id)
    {

        $data     = DB::table('settings');
        $data     = $data->where('created_by', '=', $id);
        $data     = $data->get();
        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            // "company_email" => "",
            // "company_email_from_name" => "",
            "invoice_prefix" => "#INVO",
            "journal_prefix" => "#JUR",
            "invoice_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "ffffff",
            "proposal_logo" => "2_proposal_logo.png",
            "retainer_logo" => "2_retainer_logo.png",
            "invoice_logo" => "2_invoice_logo.png",
            "bill_logo" => "2_bill_logo.png",
            "retainer_color" => "ffffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "ffffff",
            "customer_prefix" => "#CUST",
            "vender_prefix" => "#VEND",
            "contract_prefix" => "#CON",
            "retainer_prefix" => "#RET",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "retainer_template" => "template1",
            "contract_template" => "template1",
            "registration_number" => "",
            "vat_number" => "",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_number" => "on",
            "tax_type" => "",
            "shipping_display" => "on",
            "journal_prefix" => "#JUR",
            "display_landing_page" => "on",
            "title_text" => "",
            // 'gdpr_cookie' => "off",
            'cookie_text' => "",
            "twilio_sid" => "",
            "twilio_token" => "",
            "twilio_from" => "",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "",
            "SITE_RTL" => "off",
            "owner_signature" => "",
            "cust_darklayout" => "off",
            "payment_notification" => 1,
            "chatgpt_key" => '',
            "disable_lang" => '',
            "chatgpt_model" => '',
            "is_qr_enabled" => "on",
            "salesorder_qr_enabled" => "on",
            "quote_qr_enabled" => "on",
            'recaptcha_module'          => '',
            'google_recaptcha_version'  => '',
            'google_recaptcha_key'      => '',
            'google_recaptcha_secret'   => '',

        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function setting()
    {
        $data = DB::table('settings');
        $data   = $data->where('created_by', '=', 1);
        $data     = $data->get();
        $settings = [
            "cust_theme_bg" => 'on',
            "cust_darklayout" => 'off',
            'color' => 'theme-3',
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function payment_settings()
    {
        $data = DB::table('admin_payment_settings');
        if (Auth::check()) {
            $data->where('created_by', '=', Auth::user()->createId());
        } else {
            $data->where('created_by', '=', 1);
        }
        $data = $data->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public static function ownerIdforInvoice($id)
    {
        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            if ($user->type == "owner") {
                return $user->id;
            } else {
                $user1 = User::where(['id' => $user->created_by, $user->type => 'owner'])->first();
                if (!is_null($user1)) {
                    return $user->id;
                }
            }
        }
        return 0;
    }

    public static function ownerIdforSalesorder($id)
    {
        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            if ($user->type == "owner") {
                return $user->id;
            } else {
                $user1 = User::where(['id' => $user->created_by, $user->type => 'owner'])->first();
                if (!is_null($user1)) {
                    return $user->id;
                }
            }
        }
        return 0;
    }

    public static function invoice_payment_settings($id)
    {
        $data = [];

        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            $data = DB::table('admin_payment_settings');
            $data->where('created_by', '=', $user->id);
            $data = $data->get();
        }
        $res = [];

        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public static function set_payment_settings()
    {
        $data = DB::table('admin_payment_settings');

        if (Auth::check()) {
            $data->where('created_by', '=', \Auth::user()->creatorId());
        } else {
            $data->where('created_by', '=', 1);
        }
        $data = $data->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public static function error_res($msg = "", $args = array())
    {
        $msg       = $msg == "" ? "error" : $msg;
        $msg_id    = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }

    public static function get_messenger_packages_migration()
    {
        $totalMigration = 0;
        $messengerPath  = glob(base_path() . '/vendor/munafio/chatify/database/migrations' . DIRECTORY_SEPARATOR . '*.php');
        if (!empty($messengerPath)) {
            $messengerMigration = str_replace('.php', '', $messengerPath);
            $totalMigration     = count($messengerMigration);
        }

        return $totalMigration;
    }

    public static function languages()
    {
        if (self::$languages === null) {
            self::$languages = self::fetchLanguages();
        }

        return self::$languages;
    }
    public static function fetchLanguages()
    {
        $languages = Utility::langList();
        if (\Schema::hasTable('languages')) {
            $settings = self::settings();
            if (!empty($settings['disable_lang'])) {
                $disabledlang = explode(',', $settings['disable_lang']);

                $languages = Languages::whereNotIn('code', $disabledlang)->pluck('fullname', 'code');
            } else {
                $languages = Languages::pluck('fullname', 'code');
            }
        }

        return $languages;
    }


    public static function getValByName($key)
    {
        $setting = self::settings();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function getValByName1($key)
    {
        $setting = Utility::getGdpr();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }

        return $setting[$key];
    }



    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if (!file_put_contents($envFile, $str)) {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function priceFormat($settings, $price)
    {
        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_time_format'], strtotime($time));
    }

    public static function invoiceNumberFormat($settings, $number)
    {

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public static function quoteNumberFormat($settings, $number)
    {

        return $settings["quote_prefix"] . sprintf("%05d", $number);
    }

    public static function salesorderNumberFormat($settings, $number)
    {

        return $settings["salesorder_prefix"] . sprintf("%05d", $number);
    }

    public static function dateFormat($settings, $date)
    {
        return date($settings['site_date_format'], strtotime($date));
    }


    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach ($taxArr as $tax) {
            $taxes[] = ProductTax::find($tax);
        }

        return $taxes;
    }


    public static function taxRate($taxRate, $price, $quantity)
    {

        return ($taxRate / 100) * ($price * $quantity);
    }
    private static $totalTaxRate = null;

    public static function totalTaxRate($taxes)
    {
        if (self::$totalTaxRate === null) {
            self::$totalTaxRate = self::fetchtotalTaxRate($taxes);
        }

        return self::$totalTaxRate;
    }

    public static function fetchtotalTaxRate($taxes)
    {

        $taxArr  = explode(',', $taxes);
        $taxRate = 0;

        foreach ($taxArr as $tax) {

            $tax     = ProductTax::find($tax);
            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
        }

        return $taxRate;
    }

    public static function userBalance($users, $id, $amount, $type)
    {
        if ($users == 'customer') {
            $user = Customer::find($id);
        } else {
            $user = Vender::find($id);
        }

        if (!empty($user)) {
            if ($type == 'credit') {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance + $amount;
                $user->save();
            } elseif ($type == 'debit') {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance - $amount;
                $user->save();
            }
        }
    }

    public static function bankAccountBalance($id, $amount, $type)
    {
        $bankAccount = BankAccount::find($id);
        if ($bankAccount) {
            if ($type == 'credit') {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $amount;
                $bankAccount->save();
            } elseif ($type == 'debit') {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $amount;
                $bankAccount->save();
            }
        }
    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for ($i = 0; $i < count($C); ++$i) {
            if ($C[$i] <= 0.03928) {
                $C[$i] = $C[$i] / 12.92;
            } else {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if ($L > 0.179) {
            $color = 'black';
        } else {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public static function getSuperAdminValByName($key)
    {
        $data = DB::table('settings');
        $data = $data->where('name', '=', $key);
        $data = $data->first();
        if (!empty($data)) {
            $record = $data->value;
        } else {
            $record = '';
        }

        return $record;
    }

    public static function addNewData()
    {
        \Artisan::call('cache:forget spatie.permission.cache');
        \Artisan::call('cache:clear');
        $usr            = \Auth::user();
        $arrPermissions = [
            'Manage Form Builder',
            'Create Form Builder',
            'Edit Form Builder',
            'Delete Form Builder',
            'Show Form Builder',
            'Manage Form Field',
            'Create Form Field',
            'Edit Form Field',
            'Delete Form Field',
            'Manage Payment',
            'Create Payment',
            'Edit Payment',
            'Delete Payment',
            'Manage Invoice Payment',
            'Create Invoice Payment',
        ];
        foreach ($arrPermissions as $ap) {
            // check if permission is not created then create it.
            $permission = Permission::where('name', 'LIKE', $ap)->first();
            if (empty($permission)) {
                Permission::create(['name' => $ap]);
            }
        }

        $ownerRole          = Role::where('name', 'LIKE', 'Owner')->where('created_by', '=', $usr->createId())->first();
        $ownerPermissions   = $ownerRole->getPermissionNames()->toArray();
        $ownerNewPermission = [
            'Manage Form Builder',
            'Create Form Builder',
            'Edit Form Builder',
            'Delete Form Builder',
            'Show Form Builder',
            'Manage Form Field',
            'Create Form Field',
            'Edit Form Field',
            'Delete Form Field',
            'Manage Payment',
            'Create Payment',
            'Edit Payment',
            'Delete Payment',
            'Manage Invoice Payment',
            'Create Invoice Payment',
        ];
        foreach ($ownerNewPermission as $op) {
            // check if permission is not assign to owner then assign.
            if (!in_array($op, $ownerPermissions)) {
                $permission = Permission::findByName($op);
                $ownerRole->givePermissionTo($permission);
            }
        }
    }

    public static function setting_field($requestArray)
    {
        $settings = [
            "site_currency",
            "site_currency_symbol",
            "site_currency_symbol_position",
            "site_date_format",
            "site_time_format",
            "company_name",
            "company_address",
            "company_city",
            "company_state",
            "company_zipcode",
            "company_country",
            "company_telephone",
            // "company_email",
            // "company_email_from_name",
            "owner_signature",
            "invoice_prefix",
            "invoice_color",
            "quote_template",
            "quote_color",
            "salesorder_template",
            "salesorder_color",
            "proposal_prefix",
            "proposal_color",
            "bill_prefix",
            "bill_color",
            "quote_prefix",
            "salesorder_prefix",
            "vender_prefix",
            "footer_title",
            "footer_notes",
            "invoice_template",
            "bill_template",
            "proposal_template",
            "default_language",
            "enable_stripe",
            "enable_paypal",
            "paypal_mode",
            "paypal_client_id",
            "paypal_secret_key",
            "stripe_key",
            "stripe_secret",
            "decimal_number",
            "tax_type",
            "shipping_display",
            "footer_link_1",
            "footer_value_1",
            "footer_link_2",
            "footer_value_2",
            "footer_link_3",
            "footer_value_3",
            "display_landing_page",
            "title_text",
            "footer_text",
            "_token",
            "SITE_RTL",
            "color",
            "favicon",
            "logo",
            // "gdpr_cookie",
            "cookie_text",
            "company_logo",
            "company_favicon",
            "signup_button",
            "verified_button",
            "disable_lang",
        ];

        foreach ($requestArray as $key => $val) {

            if (!in_array($key, $settings)) {
                return false;
            }
        }

        return true;
    }

    public static function getselectedThemeColor()
    {
        $color = env('THEME_COLOR');
        if ($color == "" || $color == null) {
            $color = 'blue';
        }
        return $color;
    }
    public static function getAllThemeColors()
    {
        $colors = [
            'blue', 'denim', 'sapphire', 'olympic', 'violet', 'black', 'cyan', 'dark-blue-natural', 'gray', 'light-blue', 'light-purple', 'magenta', 'orange-mute', 'pale-green', 'rich-magenta', 'rich-red', 'sky-gray'
        ];
        return $colors;
    }

    public static function getDateFormated($date, $time = false)
    {
        if (!empty($date) && $date != '0000-00-00') {
            if ($time == true) {
                return date("d M Y H:i A", strtotime($date));
            } else {
                return date("d M Y", strtotime($date));
            }
        } else {
            return '';
        }
    }
    public static function ownerIdforQuote($id)
    {
        $user = User::where(['id' => $id])->first();
        if (!is_null($user)) {
            if ($user->type == "owner") {
                return $user->id;
            } else {
                $user1 = User::where(['id' => $user->created_by, $user->type => 'owner'])->first();
                if (!is_null($user1)) {
                    return $user->id;
                }
            }
        }
        return 0;
    }

    public static function send_twilio_msg($to, $slug, $obj, $user_id = null)
    {

        $notification_template = NotificationTemplates::where('slug', $slug)->first();

        if (!empty($notification_template) && !empty($obj)) {
            if (!empty($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = \Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if (empty($curr_noti_tempLang)) {
                $curr_noti_tempLang       = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content)) {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }
        if (isset($msg)) {
            $settings = Utility::settingsById($user->id);
            $account_sid = $settings['twilio_sid'];
            $auth_token = $settings['twilio_token'];
            $twilio_number = $settings['twilio_from'];
            try {
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($to, [
                    'from' => $twilio_number,
                    'body' => $msg,
                ]);
            } catch (\Exception $e) {
            }
        }
    }

    public static function send_twilio_msg_withoutauth($to, $msg, $user)
    {
        try {
            $settings  = Utility::settings($user->id);
            $account_sid    = $settings['twilio_sid'];
            $auth_token = $settings['twilio_token'];
            $twilio_number = $settings['twilio_from'];
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($to, [
                'from' => $twilio_number,
                'body' => $msg
            ]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function colorset()
    {
        if (\Auth::user()) {
            if (\Auth::user()->type == 'super admin') {
                $user = \Auth::user();
                $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
            } else {
                $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->pluck('value', 'name')->toArray();
            }
        } else {
            $user = User::where('type', 'super admin')->first();
            $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
        }
        if (!isset($setting['color'])) {
            $setting = Utility::settings();
        }
        return $setting;
    }



    public static function GetLogo()
    {
        $setting = self::settings();
        if (\Auth::user() && \Auth::user()->type != 'super admin') {

            if ($setting['cust_darklayout'] == 'on') {

                return Utility::getValByName('company_logo_light');
            } else {
                return Utility::getValByName('company_logo_dark');
            }
        } else {
            if ($setting['cust_darklayout'] == 'on') {

                return Utility::getValByName('light_logo');
            } else {
                return Utility::getValByName('dark_logo');
            }
        }
    }

    public static function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'New User',
            'Lead Assigned',
            'Task Assigned',
            'Quote Created',
            'New Sales Order',
            'New Invoice',
            'Meeting Assigned',
            'Campaign Assigned',
            'New Contract',
        ];

        foreach ($emailTemplate as $eTemp) {
            $emailTemp = EmailTemplate::where('name', $eTemp)->count();
            if ($emailTemp == 0) {
                EmailTemplate::create(
                    [
                        'name' => $eTemp,
                        'from' => env('APP_NAME'),
                        'slug' => strtolower(str_replace(' ', '_', $eTemp)),
                        'created_by' => 1,
                    ]
                );
            }
        }

        $defaultTemplate = [
            'New User' => [
                'subject' => 'Login Detail',
                'lang' => [
                    'ar' => '<p>مرحبا ، مرحبا بك في { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>البريد الالكتروني : { mail }</p>
                            <p>كلمة السرية : { password }</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>شكرا</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>Hej, velkommen til { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: { email }-</p>
                            <p>kodeord: { password }</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>Tak.</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Hallo, Willkommen bei {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>E-Mail: {email}</p>
                            <p>Kennwort: {password}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Danke,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                    'es' => '<p>Hola, Bienvenido a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>Correo electr&oacute;nico: {email}</p>
                            <p>Contrase&ntilde;a: {password}</p>
                            <p>&nbsp;</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Gracias,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Bonjour, Bienvenue dans { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: { email }</p>
                            <p>Mot de passe: { password }</p>
                            <p>{ adresse_url }</p>
                            <p>&nbsp;</p>
                            <p>Merci,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Ciao, Benvenuti in {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>Email: {email} Password: {password}</p>
                            <p>&nbsp;</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Grazie,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>こんにちは、 {app_name}へようこそ。</p>
                            <p>&nbsp;</p>
                            <p>E メール : {email}</p>
                            <p>パスワード : {password}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>ありがとう。</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Hallo, Welkom bij { app_name }.</p>
                                <p>&nbsp;</p>
                                <p>E-mail: { email }</p>
                                <p>Wachtwoord: { password }</p>
                                <p>{ app_url }</p>
                                <p>&nbsp;</p>
                                <p>Bedankt.</p>
                                <p>{ app_name }</p>',
                    'pl' => '<p>Witaj, Witamy w aplikacji {app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: {email }</p>
                            <p>Hasło: {password }</p>
                            <p>{app_url }</p>
                            <p>&nbsp;</p>
                            <p>Dziękuję,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Здравствуйте, Добро пожаловать в { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>Адрес электронной почты: { email }</p>
                            <p>Пароль: { password }</p>
                            <p>&nbsp;</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>Спасибо.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Ol&aacute;, Bem-vindo a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: {email}</p>
                            <p>Senha: {senha}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Obrigado,</p>
                            <p>{app_name}</p>
                            <p>{ имя_программы }</p>',
                    'tr' => '<p>Merhaba,&nbsp;<br>{app_name}]e hoş geldiniz.</p>
                            <p><b>E-posta </b>: {e-posta}<br><b>Şifre</b> : {şifre}</p><p>{uygulama_urlsi}</p><p>Teşekkürler,<br>{uygulama ismi}</p>',
                    'zh' => '<p>您好，<br>欢迎访问 {app_name}。</p><p><b>电子邮件 </b>: {email}<br><b>密码</b> : {password}</p><p>{app_url}</p><p>谢谢，<br>{app_name}</p>',
                    'he' => '<p>שלום, &nbsp;<br>ברוכים הבאים אל {app_name}.</p><p><b>דואל </b>: {דואל}<br><b>סיסמה</b> : {password}</p><p>{app_url}</p><p>תודה,<br>{app_name}</p>',
                    'pt-br' => '<p>Olá,&nbsp;<br>Bem-vindo ao {app_name}.</p><p><b>Email </b>: {email}<b>Senha</b> : {password}{app_url}Obrigado,{app_name}<br></p><p></p><p><br></p>',

                ],
            ],
            'Lead Assigned' => [
                'subject' => 'Lead Assign',
                'lang' => [
                    'ar' => '<p>مرحبا ، { lead_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>الرصاص الجديد تم تعيينه لك</p>
                            <p>اسم الرصاص : { lead_name }</p>
                            <p>البريد الالكتروني الرئيسي : { lead_email }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>Hello, { lead_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>New Lead er blevet tildelt.</p>
                            <p>Ledernavn: { lead_name }</p>
                            <p>Lead-e-mail: { lead_email }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<<p>Hallo, {lead_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Neuer Lead wurde Ihnen zugeordnet.</p>
                            <p>&nbsp;</p>
                            <p>Vorf&uuml;hrname: {lead_name}</p>
                            <p>Lead-E-Mail: {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Hello, {lead_assign_user}</p>
                            <p>New Lead has been Assign to you.</p>
                            <p><strong>Lead Name</strong> : {lead_name}</p>
                            <p><strong>Lead Email</strong> : {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Hello, {lead_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>New Lead ha sido Assign to you.</p>
                            <p>Nombre de cliente: {lead_name}</p>
                            <p>Correo electr&oacute;nico principal: {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Bonjour,</p>
                            <p>New Lead vous a &eacute;t&eacute; affect&eacute;.</p>
                            <p>Nom du responsable: { lead_name }</p>
                            <p>Lead Email: { lead_email }</p>
                            <p>Lead Stage: { lead_stage }</p>
                            <p>&nbsp;</p>
                            <p>Objet responsable: { lead_subject }</p>',
                    'it' => '<p>Ciao, {mem_assegna_utente}</p>
                            <p>New Lead &egrave; stato Assign a te.</p>
                            <p>Nome lead: {lead_name}</p>
                            <p>Lead Email: {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>ハロー、 {リード・アシニルユーザー}</p>
                            <p>新しいリードが割り当てられています。</p>
                            <p>リード名 : {リード _name}</p>
                            <p>リード E メール : {リード E メール}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Hallo, { lead_assign_user }</p>
                            <p>New Lead is aan u toegewezen.</p>
                            <p>Naam van lead: { lead_name }</p>
                            <p>E-mail leiden: { lead_email }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Witaj, {lead_assign_user }</p>
                            <p>Nowy kierownik został przypisany do Ciebie.</p>
                            <p>Nazwa wiodła: {lead_name }</p>
                            <p>Gł&oacute;wny adres e-mail: {lead_email }</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Здравствуйте, { lead_assign_user }</p>
                            <p>Вам назначили нового руководителя.</p>
                            <p>Имя ведущего: { lead_name }</p>
                            <p>Электронная почта: { lead_email }</p>
                            <p>&nbsp;</p>
                            <p>Привет.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Ol&aacute;, {lead_assign_user}</p>
                            <p>Nova Lideran&ccedil;a foi Assign para voc&ecirc;.</p>
                            <p>Nome do lead: {lead_name}</p>
                            <p>Email Principal: {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Merhaba, {lead_assign_user}</p>
                            <p>Yeni Müşteri Adayı size atandı.</p>
                            <p><strong>Kurşun Adı</strong> : {kurşun_adı}</p>
                            <p><strong>Potansiyel Müşteri E-postası</strong> : {lead_email}</p>
                            <p>&nbsp;</p>
                            <p>Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>Hello， {lead_assign_user}</p> <p>新商机已分配给您。</p> <p><strong>商机名称</strong> : {lead_name}</p> <p><strong>商机电子邮件</strong> : {lead_email}</p> <p></p> <p>种类注册，<br />{app_name}</p>',
                    'he' => '<p>שלום, {lead_assign_user}</p> <p>ביצוע חדש הוקצתה לכם.</p> <p><strong>שם ביצוע</strong> : {lead_name}</p> <p><strong>דואל מוביל</strong> : {lead_email}</p> <p>&nbsp;</p> <p>סוג בברכה,<br />{app_name}</p>',
                    'pt-br' => '<p>Olá, {lead_assign_user}</p>
                    <p>Novo lead foi atribuído a você.</p>
                    <p><strong>Nome do lead</strong> : {lead_name}</p>
                    <p><strong>E-mail do lead</strong> : {lead_email}</p>
                    <p>&nbsp;</p>
                    <p>Atenciosamente,<br />{app_name}</p>',
                ],
            ],

            'Task Assigned' => [
                'subject' => 'Task Assign',
                'lang' => [
                    'ar' => '<p>عزيزي ، { task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>تم تخصيصك لمهمة جديدة :</p>
                            <p>الاسم : { task_name }</p>
                            <p>تاريخ البدء : { task_start_date }</p>
                            <p>تاريخ الاستحقاق : { task_due_date }</p>
                            <p>المرحلة : { task_mattمرحلت }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>K&aelig;re, { task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Du er tildelt til en ny opgave:</p>
                            <p>Navn: { task_name }</p>
                            <p>Startdato: { task_start_date }</p>
                            <p>Forfaldsdato: { task_due_date }</p>
                            <p>Trin: { task_stage }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Sehr geehrte Frau, {task_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sie wurden einer neuen Aufgabe zugeordnet:</p>
                            <p>Name: {task_name}</p>
                            <p>Start Date: {task_start_date}</p>
                            <p>F&auml;lligkeitsdatum: {task_due_date}</p>
                            <p>Stage: {task_stage}</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Dear, {task_assign_user}</p>
                            <p>You have been assigned to a new task:</p>
                            <p style="text-align: justify;"><strong>Name</strong>: {task_name}</p>
                            <p><strong>Start Date</strong>: {task_start_date}<br /><strong>Due date</strong>: {task_due_date}<br /><strong>Stage</strong> : {task_stage}</p>
                            <p><br />Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Estimado, {task_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Se le ha asignado una nueva tarea:</p>
                            <p>Nombre: {task_name}</p>
                            <p>Fecha de inicio: {task_start_date}</p>
                            <p>Fecha de vencimiento: {task_due_date}</p>
                            <p>Etapa: {task_stage}</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Cher, { task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Vous avez &eacute;t&eacute; affect&eacute; &agrave; une nouvelle t&acirc;che:</p>
                            <p>Nom: { task_name }</p>
                            <p>Date de d&eacute;but: { task_start_date }</p>
                            <p>Date d &eacute;ch&eacute;ance: { task_due_date }</p>
                            <p>Etape: { task_stage }</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Caro, {task_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Ti &egrave; stato assegnato un nuovo incarico:</p>
                            <p>Nome: {task_name}</p>
                            <p>Data di inizio: {task_start_date}</p>
                            <p>Data di scadenza: {task_due_date}</p>
                            <p>Stage: {task_stage}</p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>Dear、 {task_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>新規タスクに割り当てられました :</p>
                            <p>名前: {task_name}</p>
                            <p>開始日: {task_start_date}</p>
                            <p>予定日: {task_due_date}</p>
                            <p>ステージ : {task_stage}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Geachte, { task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>U bent toegewezen aan een nieuwe taak:</p>
                            <p>Naam: { task_name }</p>
                            <p>Begindatum: { task_start_date }</p>
                            <p>Vervaldatum: { task_due_date }</p>
                            <p>Stadium: { task_stage }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Szanowny, {task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Użytkownik został przypisany do nowego zadania:</p>
                            <p>Nazwa: {task_name }</p>
                            <p>Data rozpoczęcia: {task_start_date }</p>
                            <p>Data zakończenia: {task_due_date }</p>
                            <p>Etap: {task_stage }</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Уважаемый, { task_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Вы назначены для новой задачи:</p>
                            <p>Имя: { task_name }</p>
                            <p>Начальная дата: { task_start_date }</p>
                            <p>Срок выполнения: { task_due_date }</p>
                            <p>Этап: { task_stage }</p>
                            <p>&nbsp;</p>
                            <p>Привет.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Querido, {task_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Voc&ecirc; foi designado para uma nova tarefa:</p>
                            <p>Nome: {task_name}</p>
                            <p>Data de in&iacute;cio: {task_start_date}</p>
                            <p>Prazo de vencimento: {task_due_date}</p>
                            <p>Est&aacute;gio: {task_stage}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Sayın {task_assign_user}</p>
                            <p>Yeni bir göreve atandınız:</p>
                            <p style="text-align: justify;"><strong>İsim</strong>: {görev adı}</p>
                            <p><strong>Başlangıç ​​tarihi</strong>: {görev_başlangıç_tarihi}<br /><strong>Bitiş tarihi</strong>: {görev_due_date}<br /><strong>Sahne</strong> : {görev_aşaması}</p>
                            <p><br />Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>Hello， {lead_assign_user}</p> <p>新商机已分配给您。</p> <p><strong>商机名称</strong> : {lead_name}</p> <p><strong>商机电子邮件</strong> : {lead_email}</p> <p></p> <p>种类注册，<br />{app_name}</p>',
                    'he' => '<p>יקירתי, {task_assign_user}</p>
                             <p>הוקצית למשימה חדשה:</p>
                             <p style="text-align: justify;"><strong>Name</strong>: {task_name}</p>
                             תאריך התחלה: {task_start_date}<br /><p><strong>תאריך</strong> <strong>יעד</strong>: {task_due_date}<br /><strong>Stage</strong> : {task_stage}</p>
                             <p><br />Kind Regards,<br />{app_name}</p>',
                    'pt-br' => '<p>Prezados, {task_assign_user}</p>
                             <p>Você foi atribuído a uma nova tarefa:</p>
                             <p style="text-align: justify;"><strong>Nome</strong>: {task_name}</p>
                             Data de Início: {task_start_date}<br /><p><strong>Data de</strong> <strong>Vencimento</strong>: {task_due_date}<br /><strong>Etapa</strong> : {task_stage}</p>
                             <br /><p>Atenciosamente,<br />{app_name}</p>',
                ],
            ],
            'Quote Created' => [
                'subject' => 'Quotation Create',
                'lang' => [
                    'ar' => '<p>عزيزي ، { quote_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>لقد تم تخصيصك لاقتباس جديد : رقم التسعير : { quote_number }</p>
                            <p>عنوان الفواتير : { billing_address }</p>
                            <p>عنوان الشحن : { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>K&aelig;re, { quote_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Du er blevet tildelt et nyt tilbud:</p>
                            <p>Tilbudsnummer: { quote_number }</p>
                            <p>Faktureringsadresse: { billing_address }</p>
                            <p>Shipping Address: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Sehr geehrte Frau, {quote_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sie wurden einem neuen Angebot zugeordnet:</p>
                            <p>Angebotsnummer: {quote_number}</p>
                            <p>Rechnungsadresse: {billing_address}</p>
                            <p>Versandadresse: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Dear, {quote_assign_user}</p>
                            <p>You have been assigned to a new quotation:</p>
                            <p><strong>Quote Number</strong> : {quote_number}</p>
                            <p><strong>Billing Address</strong> : {billing_address}</p>
                            <p><strong>Shipping Address</strong> :&nbsp; {shipping_address}</p>
                            <p><br />Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Estimado, {quote_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Se le ha asignado un nuevo presupuesto:</p>
                            <p>N&uacute;mero de presupuesto: {quote_number}</p>
                            <p>Direcci&oacute;n de facturaci&oacute;n: {billing_address}</p>
                            <p>Direcci&oacute;n de env&iacute;o: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Cher, { quote_utilisateur_quo; }</p>
                            <p>&nbsp;</p>
                            <p>Vous avez &eacute;t&eacute; affect&eacute; &agrave; un nouveau devis:</p>
                            <p>Num&eacute;ro de devis: { quote_number }</p>
                            <p>Adresse de facturation: { adresse_facturation }</p>
                            <p>Adresse d exp&eacute;dition: { adresse_livraison }</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Caro, {quote_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sei stato assegnato a una nuova quotazione:</p>
                            <p>Quote Numero: {quote_numero}</p>
                            <p>Indirizzo fatturazione: {billing_address}</p>
                            <p>Shipping Address: {indirizzo_indirizzo}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>ディア、 {quote_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>新規見積に割り当てられています。</p>
                            <p>見積番号 : {quote_number}</p>
                            <p>請求先住所 : {billing_address}</p>
                            <p>出荷先住所 : {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Geachte, { quote_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>U bent toegewezen aan een nieuwe prijsopgave:</p>
                            <p>Quote-nummer: { quote_number }</p>
                            <p>Factureringsadres: { billing_address }</p>
                            <p>Verzendadres: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Szanowny, {quote_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Użytkownik został przypisany do nowego notowania:</p>
                            <p>Numer oferty: {numer_cytowania }</p>
                            <p>Adres do faktury: {adres_faktury }</p>
                            <p>Adres dostawy: {adres_shipp_}</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Уважаемый, { quote_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Вам назначено новое предложение:</p>
                            <p>Номер предложения: { quote_number }</p>
                            <p>Адрес выставления счета: { billing_address }</p>
                            <p>Адрес доставки: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Привет.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Querido, {quote_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Voc&ecirc; foi designado para uma nova cita&ccedil;&atilde;o:</p>
                            <p>Quote N&uacute;mero: {quote_number}</p>
                            <p>Endere&ccedil;o de cobran&ccedil;a: {billing_address}</p>
                            <p>Endere&ccedil;o de envio: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Sayın {quote_assign_user}</p>
                            <p>Yeni bir teklife atandınız:</p>
                            <p><strong>Alıntı numarası</strong> : {alıntı numarası}</p>
                            <p><strong> Fatura Adresi</strong> : {Fatura Adresi}</p>
                            <p><strong>Gönderi Adresi</strong> :&nbsp; {Gönderi Adresi}</p>
                            <p><br />Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>亲爱的， {quote_assign_user}</p>
                            <p>您已分配到新的报价单：</p>
                            <p><strong>报价编号</strong> ： {quote_number}</p>
                            <p><strong>账单地址</strong> ： {billing_address}</p>
                            <p><strong>送货地址</strong> ：&nbsp; {shipping_address}</p>
                            <p><br />亲切的问候，<br />{app_name}</p>',
                    'he' => '<p>יקירתי, {quote_assign_user}</p>
                            <p>שובצת להצעת מחיר חדשה:</p>
                            <p><strong>מספר ציטוט</strong> : {quote_number}</p>
                            <p><strong>כתובת לחיוב</strong> : {billing_address}</p>
                            <p><strong>כתובת למשלוח</strong> :&nbsp; {shipping_address}</p>
                            <p><br />Kind Regards,<br />{app_name}</p>',
                    'pt-br' => '<p>Prezados, {quote_assign_user}</p>
                            <p>Você foi atribuído a uma nova cotação:</p>
                            <p><strong>Número da cotação</strong> : {quote_number}</p>
                            <p><strong>Endereço de faturamento</strong> : {billing_address}</p>
                            <p><strong>Endereço de entrega</strong> :&nbsp; {shipping_address}</p>
                            <br /><p>Atenciosamente,<br />{app_name}</p>',
                ],
            ],
            'New Sales Order' => [
                'subject' => 'Sales Order Create',
                'lang' => [
                    'ar' => '<p>عزيزي ، { مبيعات البيع _ assign_user }</p>
                            <p>&nbsp;</p>
                            <p>تم تخصيصك لعرض أسعار جديد :</p>
                            <p>رقم التسعير : { quote_number }</p>
                            <p>عنوان الفواتير : { billing_address }</p>
                            <p>عنوان الشحن : { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>K&aelig;re, { salesorder_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Du har f&aring;et tildelt et nyt tilbud:</p>
                            <p>Tilbudsnummer: { quote_number }</p>
                            <p>Faktureringsadresse: { billing_address }</p>
                            <p>Shipping Address: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Sehr geehrte Frau, {salesorder_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sie wurden einem neuen Angebot zugeordnet:</p>
                            <p>Angebotsnummer: {quote_number}</p>
                            <p>Rechnungsadresse: {billing_address}</p>
                            <p>Versandadresse: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Dear, {salesorder_assign_user}</p>
                            <p>You have been assigned to a new quotation:</p>
                            <p><strong>Quote Number</strong>&nbsp;: {quote_number}</p>
                            <p><strong>Billing Address</strong>&nbsp;: {billing_address}</p>
                            <p><strong>Shipping Address</strong>&nbsp;:&nbsp; {shipping_address}</p>
                            <p><br />Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Estimado, {salesorder_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Se le ha asignado una nueva cotizaci&oacute;n:</p>
                            <p>N&uacute;mero de presupuesto: {quote_number}</p>
                            <p>Direcci&oacute;n de facturaci&oacute;n: {billing_address}</p>
                            <p>Direcci&oacute;n de env&iacute;o: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Cher, { utilisateur_assignateur_vendeur }</p>
                            <p>&nbsp;</p>
                            <p>Vous avez &eacute;t&eacute; affect&eacute; &agrave; une nouvelle offre:</p>
                            <p>Num&eacute;ro de devis: { quote_number }</p>
                            <p>Adresse de facturation: { adresse_facturation }</p>
                            <p>Adresse d exp&eacute;dition: { adresse_livraison }</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Caro, {salesorder_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Ti &egrave; stato assegnato una nuova quotazione:</p>
                            <p>Numero preventivo: {quote_number}</p>
                            <p>Billing Address: {billing_address}</p>
                            <p>Shipping Address: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>Dear、 {salesorder_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>新しい引用符が割り当てられています。</p>
                            <p>見積もり番号 : {quote_number}</p>
                            <p>請求先住所 : {billing_address}</p>
                            <p>出荷先住所 : {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Geachte, { salesorder_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>U bent toegewezen aan een nieuwe offerte:</p>
                            <p>Quote-nummer: { quote_number }</p>
                            <p>Factureringsadres: { billing_address }</p>
                            <p>Verzendadres: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Szanowny, {salesorder_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Użytkownik został przypisany do nowego notowania:</p>
                            <p>Numer oferty: {quote_number }</p>
                            <p>Adres do faktury: {billing_address }</p>
                            <p>Adres dostawy: {shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Уважаемый, { salesorder_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Вам назначено новое предложение:</p>
                            <p>Номер предложения: { quote_number }</p>
                            <p>Адрес выставления счета: { billing_address }</p>
                            <p>Адрес доставки: { shipping_address }</p>
                            <p>&nbsp;</p>
                            <p>Привет.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Querido, {salesorder_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Voc&ecirc; foi designado para uma nova cita&ccedil;&atilde;o:</p>
                            <p>N&uacute;mero da Cota&ccedil;&atilde;o: {quote_number}</p>
                            <p>Endere&ccedil;o de Faturamento: {billing_address}</p>
                            <p>Endere&ccedil;o de Navega&ccedil;&atilde;o: {shipping_address}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Sayın {salesorder_assign_user}</p>
                            <p>Yeni bir teklife atandınız:</p>
                            <p><strong>Alıntı numarası</strong>&nbsp;: {alıntı numarası}</p>
                            <p><strong>Fatura Adresi</strong>&nbsp;: {Fatura Adresi}</p>
                            <p><strong>Gönderi Adresi</strong>&nbsp;:&nbsp; {Gönderi Adresi}</p>
                            <p><br />Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>亲爱的， {salesorder_assign_user}</p>
                            <p>您已分配到新的报价单：</p>
                            <p><strong>报价编号</strong>&nbsp;： {quote_number}</p>
                            <p><strong>账单地址</strong>&nbsp;： {billing_address}</p>
                            <p><strong>送货地址</strong>&nbsp;：&nbsp; {shipping_address}</p>
                            <p><br />亲切的问候，<br />{app_name}</p>',
                    'he' => '<p>יקירתי, {salesorder_assign_user}</p>
                            <p>שובצת להצעת מחיר חדשה:</p>
                            <p><strong>מספר</strong>&nbsp;ציטוט : {quote_number}</p>
                            <p><strong>כתובת</strong>&nbsp;לחיוב : {billing_address}</p>
                            <p><strong>כתובת</strong>&nbsp;למשלוח :&nbsp; {shipping_address}</p>
                            <p><br />Kind Regards,<br />{app_name}</p>',
                    'pt-br' => '<p>Prezados, {salesorder_assign_user}</p>
                            <p>Você foi atribuído a uma nova cotação:</p>
                            <p>Número&nbsp;<strong>da cotação</strong> : {quote_number}</p>
                            <p><strong>Endereço</strong>&nbsp;de faturamento : {billing_address}</p>
                            <p><strong>Endereço</strong>&nbsp;de entrega :&nbsp; {shipping_address}</p>
                            <br /><p>Atenciosamente,<br />{app_name}</p>',
                ],
            ],
            'New Invoice' => [
                'subject' => 'Invoice Create',
                'lang' => [
                    'ar' => 'العزيز<span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span><br><br>لقد قمنا بإعداد الفاتورة التالية من أجلك<span style="font-size: 12pt;">: </span><strong style="font-size: 12pt;">&nbsp;{invoice_id}</strong><br><br>حالة الفاتورة<span style="font-size: 12pt;">: {invoice_status}</span><br><br><br>يرجى الاتصال بنا للحصول على مزيد من المعلومات<span style="font-size: 12pt;">.</span><br><br>أطيب التحيات<span style="font-size: 12pt;">,</span><br>{app_name}',
                    'da' => 'Kære<span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span><br><br>Vi har udarbejdet følgende faktura til dig<span style="font-size: 12pt;">:&nbsp;&nbsp;{invoice_id}</span><br><br>Fakturastatus: {invoice_status}<br><br>Kontakt os for mere information<span style="font-size: 12pt;">.</span><br><br>Med venlig hilsen<span style="font-size: 12pt;">,</span><br>{app_name}',
                    'de' => '<p><b>sehr geehrter</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><br><br>Wir haben die folgende Rechnung für Sie vorbereitet<span style="font-size: 12pt;">: {invoice_id}</span><br><br><b>Rechnungsstatus</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Bitte kontaktieren Sie uns für weitere Informationen<span style="font-size: 12pt;">.</span><br><br><b>Mit freundlichen Grüßen</b><span style="font-size: 12pt;">,</span><br>{app_name}</p>',
                    'en' => '<p><span style="font-size: 12pt;"><strong>Dear</strong>&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p>
                            <p><span style="font-size: 12pt;">We have prepared the following invoice for you :#{invoice_id}</span></p>
                            <p><span style="font-size: 12pt;"><strong>Invoice Status</strong> : {invoice_status}</span></p>
                            <p>Please Contact us for more information.</p>
                            <p><span style="font-size: 12pt;">&nbsp;</span></p>
                            <p><strong>Kind Regards</strong>,<br /><span style="font-size: 12pt;">{app_name}</span></p>',
                    'es' => '<p><b>Querida</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Hemos preparado la siguiente factura para ti<span style="font-size: 12pt;">:&nbsp;&nbsp;{invoice_id}</span></p><p><b>Estado de la factura</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Por favor contáctenos para más información<span style="font-size: 12pt;">.</span></p><p><b>Saludos cordiales</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'fr' => '<p><b>Cher</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Nous avons préparé la facture suivante pour vous<span style="font-size: 12pt;">: {invoice_id}</span></p><p><b>État de la facture</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Veuillez nous contacter pour plus d\'informations<span style="font-size: 12pt;">.</span></p><p><b>Sincères amitiés</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'it' => '<p><b>Caro</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Abbiamo preparato per te la seguente fattura<span style="font-size: 12pt;">:&nbsp;&nbsp;{invoice_id}</span></p><p><b>Stato della fattura</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Vi preghiamo di contattarci per ulteriori informazioni<span style="font-size: 12pt;">.</span></p><p><b>Cordiali saluti</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'ja' => '親愛な<span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span><br><br>以下の請求書をご用意しております。<span style="font-size: 12pt;">: {invoice_client}</span><br><br>請求書のステータス<span style="font-size: 12pt;">: {invoice_status}</span><br><br>詳しくはお問い合わせください<span style="font-size: 12pt;">.</span><br><br>敬具<span style="font-size: 12pt;">,</span><br>{app_name}',
                    'nl' => '<p><b>Lieve</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>We hebben de volgende factuur voor u opgesteld<span style="font-size: 12pt;">: {invoice_id}</span></p><p><b>Factuurstatus</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Voor meer informatie kunt u contact met ons opnemen<span style="font-size: 12pt;">.</span></p><p><b>Vriendelijke groeten</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'pl' => '<p><b>Drogi</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Przygotowaliśmy dla Ciebie następującą fakturę<span style="font-size: 12pt;">: {invoice_id}</span></p><p><b>Status faktury</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Skontaktuj się z nami, aby uzyskać więcej informacji<span style="font-size: 12pt;">.</span></p><p><b>Z poważaniem</b><span style="font-size: 12pt;"><b>,</b><br></span>{app_name}</p>',
                    'ru' => '<p><b>дорогая</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Мы подготовили для вас следующий счет<span style="font-size: 12pt;">: {invoice_id}</span></p><p><b>Статус счета</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Пожалуйста, свяжитесь с нами для получения дополнительной информации<span style="font-size: 12pt;">.</span></p><p><b>С уважением</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'pt' => '<p><b>Querida</b><span style="font-size: 12pt;">&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p><p>Preparamos a seguinte fatura para você<span style="font-size: 12pt;">: {invoice_id}</span></p><p><b>Status da fatura</b><span style="font-size: 12pt;">: {invoice_status}</span></p><p>Entre em contato conosco para mais informações.<span style="font-size: 12pt;">.</span></p><p><b>Atenciosamente</b><span style="font-size: 12pt;">,<br></span>{app_name}</p>',
                    'en' => '<p><span style="font-size: 12pt;"><strong>Canım</strong>&nbsp;{fatura_istemcisi}</span><span style="font-size: 12pt;">,</span><p>
                            <p><span style="font-size: 12pt;">Aşağıdaki faturayı sizin için hazırladık :#{fatura_kimliği}</span></p>
                            <p><span style="font-size: 12pt;"><strong>Fatura Durumu</strong> : {fatura_durumu}</span></p>
                            <p>Daha fazla bilgi için lütfen bizimle iletişime geçin.</p>
                            <p><span style="font-size: 12pt;">&nbsp;</span></p>
                            <p><strong>Saygılarımla</strong>,<br /><span style="font-size: 12pt;">{uygulama ismi}</span></p>',
                    'zh' => '<p><span style=“font-size： 12pt;”><strong>Dear</strong>&nbsp;{invoice_client}</span><span style=“font-size： 12pt;”>，</span></p>
                            <p><span style=“font-size： 12pt;”>我们为您准备了以下发票：#{invoice_id}</span></p>
                            <p><span style=“font-size： 12pt;”><strong>发票状态</strong> ： {invoice_status}</span></p>
                            <p>请联系我们获取更多信息。</p>
                            <p><span style=“font-size： 12pt;”>&nbsp;</span></p>
                            <p><strong>亲切的问候</strong>，<br /><span style=“font-size： 12pt;”>{app_name}</span></p>',
                    'he' => '<p><span style="font-size: 12pt;"><strong>Dear</strong>&nbsp;{invoice_client}</span><span style="font-size: 12pt;">,</span></p>
                            <p><span style="font-size: 12pt;">הכנו עבורכם את החשבונית הבאה :#{invoice_id}</span></p>
                            <p><span style="font-size: 12pt;"><strong>מצב חשבונית</strong> : {invoice_status}</span></p>
                            <p>אנא צרו איתנו קשר לקבלת מידע נוסף.</p>
                            <p><span style="font-size: 12pt;">&nbsp;</span></p>
                            <p><strong>בברכה</strong>,<br /><span style="font-size: 12pt;">{app_name}</span></p>',
                    'pt-br' => '<p><span style="font-size: 12pt;"><strong>Caro</strong>&nbsp;{invoice_client}<span style="font-size: 12pt;"></span>,</span></p>
                            <p><span style="font-size: 12pt;">Preparamos a seguinte fatura para você:#{invoice_id}</span></p>
                            <p><span style="font-size: 12pt;"><strong>Status da fatura</strong>: {invoice_status}</span></p>
                            <p>Entre em contato conosco para mais informações.</p>
                            <p><span style="tamanho da fonte: 12pt;">&nbsp;</span></p>
                            <p><strong>Atenciosamente</strong>,<br /><span style="font-size: 12pt;">{app_name}</span></p>',
                ],
            ],
            'Meeting Assigned' => [
                'subject' => 'Meeting Assign',
                'lang' => [
                    'ar' => '<p>عزيزي ، { attasing_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>تم تخصيصك لاجتماع جديد :</p>
                            <p>الاسم : { attabing_name }</p>
                            <p>تاريخ البدء : { attabing_start_date }</p>
                            <p>تاريخ الاستحقاق : { batuinging_duse_date }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>K&aelig;re, { meeting_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Du er blevet tildelt til et nyt m&oslash;de:</p>
                            <p>Navn: { meeting_name }</p>
                            <p>Startdato: { meeting_start_date }</p>
                            <p>Forfaldsdato: { meeting_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Sehr geehrte Frau, {meeting_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sie wurden einer neuen Besprechung zugeordnet:</p>
                            <p>Name: {meeting_name}</p>
                            <p>Start Date: {meeting_start_date}</p>
                            <p>F&auml;lligkeitsdatum: {meeting_due_date}</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Dear, {meeting_assign_user}</p>
                            <p>You have been assigned to a new meeting:</p>
                            <p><strong>Name</strong>: {meeting_name}<br /><strong>Start Date</strong>: {meeting_start_date}<br /><strong>Due date</strong>: {meeting_due_date}<br /><br /><br />Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Estimado, {meeting_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Se le ha asignado una nueva reuni&oacute;n:</p>
                            <p>Nombre: {meeting_name}</p>
                            <p>Fecha de inicio: {meeting_start_date}</p>
                            <p>Fecha de vencimiento: {meeting_due_date}</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Cher, { meeting_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Vous avez &eacute;t&eacute; affect&eacute; &agrave; une nouvelle r&eacute;union:</p>
                            <p>Nom: { meeting_name }</p>
                            <p>Date de d&eacute;but: { meeting_start_date }</p>
                            <p>Date d &eacute;ch&eacute;ance: { meeting_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Caro, {meeting_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Ti &egrave; stato assegnato un nuovo incontro:</p>
                            <p>Nome: {meeting_name}</p>
                            <p>Data di inizio: {meeting_start_date}</p>
                            <p>Data di scadenza: {meeting_due_date}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>デッドロック、 {meeting_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>新規ミーティングに割り当てられました :</p>
                            <p>名前: {meeting_name}</p>
                            <p>開始日: {meeting_start_date}</p>
                            <p>予定日: {meeting_due_date}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Dear, { meeting_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>U bent toegewezen aan een nieuwe vergadering:</p>
                            <p>Naam: { meeting_name }</p>
                            <p>Begindatum: { meeting_start_date }</p>
                            <p>Vervaldatum: { meeting_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Szanowny, {meeting_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Użytkownik został przypisany do nowego spotkania:</p>
                            <p>Nazwa: {meeting_name }</p>
                            <p>Data rozpoczęcia: {meeting_start_date }</p>
                            <p>Termin realizacji: {meeting_due_date }</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Уважаемый, { meeting_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Вам назначено новое собрание:</p>
                            <p>Имя: { meeting_name }</p>
                            <p>Начальная дата: { meeting_start_date }</p>
                            <p>Дата выполнения: { meeting_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Привет.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Querido, {meeting_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Voc&ecirc; foi designado para uma nova reuni&atilde;o:</p>
                            <p>Nome: {meeting_name}</p>
                            <p>Data de in&iacute;cio: {meeting_start_date}</p>
                            <p>Prazo de vencimento: {meeting_due_date}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Sayın {meeting_assign_user}</p>
                            <p>Yeni bir toplantıya atandınız:</p>
                            <p><strong>İsim</strong>: {toplantı_adı}<br /><strong>Başlangıç ​​tarihi</strong>: {toplantı_başlangıç_tarihi}<br /><strong>Bitiş tarihi</strong>: {toplantı_due_date}<br /><br /><br />Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>亲爱的， {meeting_assign_user}</p>
                            <p>您已分配到新会议：</p>
                            名称： {meeting_name}<br />开始日期： {meeting_start_date}<br />截止日期： {meeting_due_date}<br /><br /><br /><p><strong></strong>亲切的问候，<br />{app_name}<strong></strong><strong></strong></p>',
                    'he' => '<p>יקירתי, {meeting_assign_user}</p>
                             <p>שובצת לפגישה חדשה:</p>
                             שם: {meeting_name}<br />תאריך התחלה: {meeting_start_date}<br />תאריך יעד: {meeting_due_date}<br /><br /><br /><p><strong></strong>Kind Regards,<br />{app_name}<strong></strong><strong></strong></p>',
                    'pt-br' => '<p>Prezados, {meeting_assign_user}</p>
                            <p>Você foi designado para uma nova reunião:</p>
                            Nome: {meeting_name}<br />Data de início: {meeting_start_date}<br />Data de vencimento: {meeting_due_date}<br /><br /><br /><p><strong></strong>Atenciosamente,<br />{app_name}<strong></strong><strong></strong></p>',
                ],
            ],
            'Campaign Assigned' => [
                'subject' => 'Campaign Assign',
                'lang' => [
                    'ar' => '<p>عزيزي { signlign_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>لقد تم تعيينك لحملة جديدة</p>
                            <p>الاسم : { dlralign_title }</p>
                            <p>تاريخ البدء : { dlignlign_start_date }</p>
                            <p>تاريخ الاستحقاق : { mievign_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Regards نوع ،</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>K&aelig;re { kampaggn_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Du er tildelt til en ny kampagne:</p>
                            <p>Navn: { kampaggn_title }</p>
                            <p>Startdato: { kampaggn_start_date }</p>
                            <p>Forfaldsdato: { kampaggn_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Kind Hilds,</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Sehr geehrter {campaign_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Sie wurden einer neuen Kampagne zugeordnet:</p>
                            <p>Name: {campaign_title}</p>
                            <p>Anfangsdatum: {campaign_start_date}</p>
                            <p>F&auml;lligkeitsdatum: {campaign_due_date}</p>
                            <p>&nbsp;</p>
                            <p>G&uuml;tige Gr&uuml;&szlig;e,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Dear {campaign_assign_user}</p>
                            <p>You have been assigned to a new campaign:</p>
                            <p><strong>Name</strong>: {campaign_title}<br /><strong>Start Date</strong>: {campaign_start_date}<br /><strong>Due date</strong>: {campaign_due_date}<br /><br /><br />Kind Regards,<br />{app_name}</p>',
                    'es' => '<p>Estimado {campaign_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Usted ha sido asignado a una nueva campa&ntilde;a:</p>
                            <p>Nombre: {campaign_title}</p>
                            <p>Fecha de inicio: {campaign_start_date}</p>
                            <p>Fecha de vencimiento: {campaign_due_date}</p>
                            <p>&nbsp;</p>
                            <p>Bondadoso,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Cher { milit_assign_utilisateur }</p>
                            <p>&nbsp;</p>
                            <p>Vous avez &eacute;t&eacute; affect&eacute; &agrave; une nouvelle campagne:</p>
                            <p>Nom: { campaign_title }</p>
                            <p>Date de d&eacute;but: { campaign_start_date }</p>
                            <p>Date d&eacute;ch&eacute;ance: { milit_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Caro {campagne _assign_utente}</p>
                            <p>&nbsp;</p>
                            <p>Sei stato assegnato ad una nuova campagna:</p>
                            <p>Nome: {campagne _title}</p>
                            <p>Data di inizio: {campagne _start_date}</p>
                            <p>Data di scadenza: {campagne _due_data}</p>
                            <p>&nbsp;</p>
                            <p>Kind Regards,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>{campaign_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>新規キャンペーンに割り当てられました :</p>
                            <p>名前: {campaign_title}</p>
                            <p>開始日: {campaign_start_date}</p>
                            <p>期限日 : {campaign_due_date}</p>
                            <p>&nbsp;</p>
                            <p>カンド・リーカード</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Geachte { campingign_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>U bent toegewezen aan een nieuwe campagne:</p>
                            <p>Naam: { campaign_title }</p>
                            <p>Begindatum: { campaign_start_date }</p>
                            <p>Vervaldatum: { campaign_due_date }</p>
                            <p>&nbsp;</p>
                            <p>Vriendelijke groeten,</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Szanowny użytkowniku {campaign_assign_user }</p>
                            <p>&nbsp;</p>
                            <p>Użytkownik został przypisany do nowej kampanii:</p>
                            <p>Nazwa: {campaign_title }</p>
                            <p>Data rozpoczęcia: {campaign_start_date }</p>
                            <p>Termin realizacji: {campaign_due_date }</p>
                            <p>&nbsp;</p>
                            <p>W Odniesieniu Do Rodzaju,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Уважаемый { пользователь-ascampaign_user</p>
                                <p>&nbsp;</p>
                                <p>Вас назначили для новой кампании:</p>
                                <p>Имя: { campaign_title }</p>
                                <p>Начальная дата: { campaign_start_date }</p>
                                <p>Дата выполнения: { campaign_due_date }</p>
                                <p>&nbsp;</p>
                                <p>Привет.</p>
                                <p>{ имя_программы }</p>',
                    'pt' => '<p>Querido {campaign_assign_user}</p>
                            <p>&nbsp;</p>
                            <p>Voc&ecirc; foi designado para uma nova campanha:</p>
                            <p>Nome: {campaign_title}</p>
                            <p>Data de in&iacute;cio: {campaign_start_date}</p>
                            <p>Prazo de vencimento: {campaign_due_date}</p>
                            <p>&nbsp;</p>
                            <p>Esp&eacute;cie Considera,</p>
                            <p>{app_name}</p>',
                    'tr' => '<p>Sayın {campaign_assign_user}</p>
                            <p>Yeni bir kampanyaya atandınız:</p>
                            <p><strong>İsim</strong>: {kampanya_başlığı}<br /><strong>Başlangıç ​​tarihi</strong>: {kampanya_başlangıç_tarihi}<br /><strong>Bitiş tarihi</strong>: {kampanya_due_date}<br /><br /><br />Saygılarımla,<br />{uygulama ismi}</p>',
                    'zh' => '<p>亲爱的{campaign_assign_user}</p>
                            <p>您已分配到新的广告系列：</p>
                            名称： {campaign_title}<br />开始日期： {campaign_start_date}<br />截止日期： {campaign_due_date}<br /><br /><br /><p><strong></strong>亲切的问候，<br />{app_name}<strong></strong><strong></strong></p>',
                    'he' => '<p>יקר {campaign_assign_user}</p>
                            <p>שובצת לקמפיין חדש:</p>
                            שם: {campaign_title}<br />תאריך התחלה: {campaign_start_date}<br />תאריך יעד: {campaign_due_date}<br /><br /><br /><p><strong></strong>Kind Regards,<br />{app_name}<strong></strong><strong></strong></p>',
                    'pt-br' => '<p>Prezado {campaign_assign_user}</p>
                            <p>Você foi atribuído a uma nova campanha:</p>
                            Nome: {campaign_title}<br />Data de início: {campaign_start_date}<br />Data de vencimento: {campaign_due_date}<br /><br /><br /><p><strong></strong>Atenciosamente,<br />{app_name}<strong></strong><strong></strong></p>',
                ],
            ],
            'New Contract' => [
                'subject' => 'Contract',
                'lang' => [
                    'ar' => '<p>مرحبا { contract_client }</p>
                    <p>موضوع العقد : { contract_subject }</p>
                    <p>تاريخ البدء : { contract_start_date }</p>
                    <p>تاريخ الانتهاء : { contract_end_date } .</p>
                    <p>أتطلع لسماع منك</p>
                    <p>&nbsp;</p>
                    <p>Regards,</p>
                    <p>{ company_name }</p>',
                    'da' => '<p>Hej { contract_client }</p>
                    <p>Kontraktemne: { contract_subject }</p>
                    <p>Startdato: { contract_start_date }</p>
                    <p>Slutdato: { contract_end_date }</p>
                    <p>Jeg gl&aelig;der mig til at h&oslash;re fra dig.</p>
                    <p>&nbsp;</p>
                    <p>Med venlig hilsen</p>
                    <p>{ company_name }</p>',
                    'de' => '<p>Hi {contract_client}</p>
                    <p>Vertragsgegenstand: {contract_subject}</p>
                    <p>Startdatum: {contract_start_date}</p>
                    <p>Enddatum: {contract_end_date}</p>
                    <p>Freuen Sie sich auf das H&ouml;ren von Ihnen.</p>
                    <p>&nbsp;</p>
                    <p>Betrachtet,</p>
                    <p>{company_name}</p>',
                    'es' => '<p>Hi {contract_client}</p>
                    <p>Asunto del contrato: {contract_subject}</p>
                    <p>Fecha de inicio: {contract_start_date}</p>
                    <p>Fecha de finalizaci&oacute;n: {contract_end_date}</p>
                    <p>Con ganas de escuchar de ti.</p>
                    <p>&nbsp;</p>
                    <p>Considerando,</p>
                    <p>{company_name}</p>',
                    'en' => '<p>Hi {contract_client}</p>
                    <p>Contract Subject: {contract_subject}</p>
                    <p>Start Date: {contract_start_date}</p>
                    <p>End Date: {contract_end_date}</p>
                    <p>Looking forward to hear from you.</p>
                    <p>&nbsp;</p>
                    <p>Regards,</p>
                    <p>{company_name}</p>',
                    'fr' => '<p>Bonjour { contract_client }</p>
                    <p>Objet du contrat: { contract_subject }</p>
                    <p>Date de d&eacute;but: { contract_start_date }</p>
                    <p>Date de fin: { contract_end_date }</p>
                    <p>Vous avez h&acirc;te de vous entendre.</p>
                    <p>&nbsp;</p>
                    <p>Regards,</p>
                    <p>{ company_name }</p>',
                    'it' => '<p>Ciao {contract_client}</p>
                    <p>Oggetto contratto: {contract_subject}</p>
                    <p>Data inizio: {contract_start_date}</p>
                    <p>Data di fine: {contract_end_date}</p>
                    <p>Non vedo lora di sentirti.</p>
                    <p>&nbsp;</p>
                    <p>Riguardo,</p>
                    <p>{company_name}</p>',
                    'ja' => '<p>こんにちは {contract_client}</p>
                    <p>契約件名: {contract_subject}</p>
                    <p>開始日: {contract_start_date}</p>
                    <p>終了日: {contract_end_date}</p>
                    <p>あなたからの便りを楽しみにしています</p>
                    <p>&nbsp;</p>
                    <p>よろしく</p>
                    <p>{ company_name}</p>',
                    'nl' => '<p>Hallo { contract_client }</p>
                    <p>Contractonderwerp: { contract_subject }</p>
                    <p>Begindatum: { contract_start_date }</p>
                    <p>Einddatum: { contract_end_date }</p>
                    <p>Ik kijk ernaar uit om van je te horen.</p>
                    <p>&nbsp;</p>
                    <p>Betreft:</p>
                    <p>{ company_name }</p>',
                    'pl' => '<p>Witaj {contract_client }</p>
                    <p>Temat kontraktu: {contract_subject }</p>
                    <p>Data rozpoczęcia: {contract_start_date }</p>
                    <p>Data zakończenia: {contract_end_date }</p>
                    <p>Nie mogę się doczekać, by usłyszeć od ciebie.</p>
                    <p>&nbsp;</p>
                    <p>W odniesieniu do</p>
                    <p>{company_name }</p>',
                    'pt' => '<p>Oi {contract_client}</p>
                    <p>Assunto do Contrato: {contract_subject}</p>
                    <p>Data de In&iacute;cio: {contract_start_date}</p>
                    <p>Data de encerramento: {contract_end_date}</p>
                    <p>Olhando para a frente para ouvir de voc&ecirc;.</p>
                    <p>&nbsp;</p>
                    <p>Considera,</p>
                    <p>{company_name}</p>',
                    'ru' => '<p>Здравствуйте { contract_client }</p>
                    <p>Тема договора: { contract_subject }</p>
                    <p>Дата начала: { contract_start_date }</p>
                    <p>Дата окончания: { contract_end_date }</p>
                    <p>С нетерпением жду услышать от тебя.</p>
                    <p>&nbsp;</p>
                    <p>С уважением,</p>
                    <p>{ company_name }</p>',
                    'tr' => '<p>Merhaba {sözleşme_istemcisi}</p>
                    <p>Sözleşme Konusu: {contract_subject}</p>
                    <p>Başlangıç ​​Tarihi: {contract_start_date}</p>
                    <p>Bitiş Tarihi: {contract_end_date}</p>
                    <p>senden haber bekliyorum.</p>
                    <p>&nbsp;</p>
                    <p>Saygılarımızla,</p>
                    <p>{Firma Adı}</p>',
                    'zh' => '<p>嗨{contract_client}</p>
                    <p>合同标的： {contract_subject}</p>
                    <p>开始日期：{contract_start_date}</p>
                    <p>结束日期：{contract_end_date}</p>
                    <p>期待您的来信。</p>
                    <p>&nbsp;</p>
                    <p>问候</p>
                    <p>{company_name}</p>',
                    'he' => '<p>היי {contract_client}</p>
                    <p>נושא החוזה: {contract_subject}</p>
                    <p>תאריך התחלה: {contract_start_date}</p>
                    <p>תאריך סיום: {contract_end_date}</p>
                    <p>מצפה לשמוע ממך.</p>
                    <p>&nbsp;</p>
                    <p>ברכות</p>
                    <p>{company_name}</p>',
                    'pt-br' => '<p>Oi {contract_client}</p>
                    <p>Objeto do contrato: {contract_subject}</p>
                    <p>Data de início: {contract_start_date}</p>
                    <p>Data de fim: {contract_end_date}</p>
                    <p>Ansioso para ouvir de você.</p>
                    <p>&nbsp;</p>
                    <p>Relação</p>
                    <p>{company_name}</p>',
                ],

            ],
        ];

        $email = EmailTemplate::all();
        foreach ($email as $e) {
            foreach ($defaultTemplate[$e->name]['lang'] as $lang => $content) {
                $emailNoti = EmailTemplateLang::where('parent_id', $e->id)->where('lang', $lang)->count();
                if ($emailNoti == 0) {
                    EmailTemplateLang::create(
                        [
                            'parent_id' => $e->id,
                            'lang' => $lang,
                            'subject' => $defaultTemplate[$e->name]['subject'],
                            'content' => $content,
                        ]
                    );
                }
            }
        }
    }
    public static function userDefaultData()
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();

        foreach ($allEmail as $email) {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => 2,
                    'is_active' => 0,
                ]
            );
        }
    }

    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
    {
        $usr = \Auth::user();
        //Remove Current Login user Email don't send mail to them
        // if ($usr) {
        // if (is_array($mailTo)) {
        // unset($mailTo[$usr->id]);
        //
        // $mailTo = array_values($mailTo);
        // dd($mailTo);
        // }
        // }
        // find template is exist or not in our record
        $template = EmailTemplate::where('slug', $emailTemplate)->first();
        if (isset($template) && !empty($template)) {

            // check template is active or not by company
            if ($usr->type != 'super admin') {
                $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->first();
            } else {
                $is_active = (object) array('is_active' => 1);
            }
            if ($is_active->is_active == 1) {
                $settings = self::settings();
                $setting = self::settingsById($usr->id);


                if ($usr) {
                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();
                } else {
                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', 'en')->first();
                }
                $content['from'] = $template->from;

                if (!empty($content->content)) {
                    $content->content = self::replaceVariable($content->content, $obj);
                    // send email
                    try {
                        config([
                            'mail.driver'       => $settings['mail_driver'] ? $settings['mail_driver'] : $setting['mail_driver'],
                            'mail.host'         => $settings['mail_host'] ? $settings['mail_host'] : $setting['mail_host'],
                            'mail.port'         => $settings['mail_port'] ? $settings['mail_port'] : $setting['mail_port'],
                            'mail.encryption'   => $settings['mail_encryption'] ? $settings['mail_encryption'] : $setting['mail_encryption'],
                            'mail.username'     => $settings['mail_username'] ? $settings['mail_username'] : $setting['mail_username'],
                            'mail.password'     => $settings['mail_password'] ? $settings['mail_password'] : $setting['mail_password'],
                            'mail.from.address' => $settings['mail_from_address'] ? $settings['mail_from_address'] : $setting['mail_from_address'],
                            'mail.from.name'    => $settings['mail_from_name'] ? $settings['mail_from_name'] : $setting['mail_from_name'],
                        ]);
                        Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                    } catch (\Exception $e) {
                        $error = __('E-Mail has been not sent due to SMTP configuration');
                    }

                    if (isset($error)) {
                        $arReturn = [
                            'is_success' => false,
                            'error' => $error,
                        ];
                    } else {
                        $arReturn = [
                            'is_success' => true,
                            'error' => false,
                        ];
                    }
                } else {
                    $arReturn = [
                        'is_success' => false,
                        'error' => __('Mail not send, email is empty'),
                    ];
                }

                return $arReturn;
            } else {
                return [
                    'is_success' => true,
                    'error' => false,
                ];
            }
        }
    }

    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{lead_name}',
            '{lead_email}',
            '{lead_subject}',
            '{lead_pipeline}',
            '{lead_stage}',
            '{lead_assign_user}',
            '{task_name}',
            '{task_priority}',
            '{task_start_date}',
            '{task_due_date}',
            '{task_stage}',
            '{task_assign_user}',
            '{task_description}',
            '{billing_address}',
            '{shipping_address}',
            '{quote_number}',
            '{quote_assign_user}',
            '{date_quoted}',
            '{salesorder_assign_user}',
            '{invoice_id}',
            '{invoice_client}',
            '{invoice_issue_date}',
            '{created_at}',
            '{invoice_status}',
            '{invoice_total}',
            '{invoice_sub_total}',
            '{invoice_due_amount}',
            '{attendees_user}',
            '{attendees_contact}',
            '{meeting_name}',
            '{meeting_start_date}',
            '{meeting_due_date}',
            '{meeting_assign_user}',
            '{meeting_description}',
            '{campaign_assign_user}',
            '{campaign_description}',
            '{campaign_start_date}',
            '{campaign_due_date}',
            '{campaign_title}',
            '{campaign_status}',
            '{contract_subject}',
            '{contract_client}',
            '{contract_start_date}',
            '{contract_end_date}',
            '{app_name}',
            '{company_name}',
            '{app_url}',
            '{email}',
            '{password}',
            '{lead_description}',
            '{user_name}',
            '{amount}',
            '{payment_type}',
        ];
        $arrValue    = [
            'lead_name' => '-',
            'lead_email' => '-',
            'lead_subject' => '-',
            'lead_pipeline' => '-',
            'lead_stage' => '-',
            'lead_assign_user' => '-',
            'task_name' => '-',
            'task_priority' => '-',
            'task_start_date' => '-',
            'task_due_date' => '-',
            'task_stage' => '-',
            'task_assign_user' => '-',
            'task_description' => '-',
            'billing_address' => '-',
            'shipping_address' => '-',
            'quote_number' => '-',
            'quote_assign_user' => '-',
            'date_quoted' => '-',
            'salesorder_assign_user' => '-',
            'invoice_id' => '-',
            'invoice_client' => '-',
            'invoice_issue_date' => '-',
            'created_at' => '-',
            'invoice_status' => '-',
            'invoice_total' => '-',
            'invoice_sub_total' => '-',
            'invoice_due_amount' => '-',
            'attendees_user' => '-',
            'attendees_contact' => '-',
            'meeting_name' => '-',
            'meeting_start_date' => '-',
            'meeting_due_date' => '-',
            'meeting_assign_user' => '-',
            'meeting_description' => '-',
            'campaign_assign_user' => '-',
            'campaign_description' => '-',
            'campaign_start_date' => '-',
            'campaign_due_date' => '-',
            'campaign_title' => '-',
            'campaign_status' => '-',
            'contract_subject' => '-',
            'contract_client' => '-',
            'contract_start_date' => '-',
            'contract_end_date' => '-',
            'app_name' => '-',
            'company_name' => '-',
            'app_url' => '-',
            'email' => '-',
            'password' => '-',
            'lead_description' => '-',
            'user_name' => '-',
            'amount' => '-',
            'payment_type' => '',
        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        $settings = Utility::settings();
        $company_name = $settings['company_name'];

        $arrValue['app_name']     =  $company_name;
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url']      = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }



    public static function updateUserDefaultEmailTempData()
    {
        $UserEmailTemp = UserEmailTemplate::groupBy('user_id')->pluck('user_id');
        $allUser = User::where('type', 'owner')->whereNotIn('id', $UserEmailTemp)->get();

        foreach ($allUser as $user) {

            $allEmail = EmailTemplate::all();

            foreach ($allEmail as $email) {
                UserEmailTemplate::create(
                    [
                        'template_id' => $email->id,
                        'user_id' => $user->id,
                        'is_active' => 1,
                    ]
                );
            }
        }
    }

    public static function getStorageSetting()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1);

        $data     = $data->get();
        $settings = [
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",

        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }
    public static function getSeoSetting()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1);

        $data     = $data->get();
        $settings = [
            "meta_keywords" => "",
            "meta_image" => "",
            "meta_description" => ""
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function upload_file($request, $key_name, $name, $path, $custom_validation = [])
    {
        try {
            $settings = self::settings();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com'
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes =  !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';
                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes =  !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';
                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes =  !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }


                $file = $request->$key_name;


                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];
                }
                $validator = \Validator::make($request->all(), [
                    $key_name => $validation
                ]);

                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {
                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path . $name;
                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;

                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                        // $path = $path.$name;
                    }


                    $res = [
                        'flag' => 1,
                        'msg'  => 'success',
                        'url'  => $path
                    ];
                    return $res;
                }
            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }
        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }



    public static function get_file($path)
    {
        $settings = self::settings();
        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com'
                    ]
                );
            } elseif ($settings['storage_setting'] == 's3') {
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function colorCodeData($type)
    {
        if ($type == 'event') {
            return 1;
        } elseif ($type == 'zoom_meeting') {
            return 2;
        } elseif ($type == 'task') {
            return 3;
        } elseif ($type == 'appointment') {
            return 11;
        } elseif ($type == 'rotas') {
            return 3;
        } elseif ($type == 'holiday') {
            return 4;
        } elseif ($type == 'call') {
            return 10;
        } elseif ($type == 'meeting') {
            return 5;
        } elseif ($type == 'leave') {
            return 6;
        } elseif ($type == 'work_order') {
            return 7;
        } elseif ($type == 'lead') {
            return 7;
        } elseif ($type == 'deal') {
            return 8;
        } elseif ($type == 'interview_schedule') {
            return 9;
        } else {
            return 11;
        }
    }

    public static $colorCode = [
        1 => 'event-warning',
        2 => 'event-secondary',
        3 => 'event-success',
        4 => 'event-warning',
        5 => 'event-danger',
        6 => 'event-dark',
        7 => 'event-black',
        8 => 'event-info',
        9 => 'event-secondary',
        10 => 'event-success',
        11 => 'event-warning',

    ];

    public static function googleCalendarConfig()
    {
        $setting = Utility::settings();
        $path = storage_path($setting['google_calender_json_file']);
        config([
            'google-calendar.default_auth_profile' => 'service_account',
            'google-calendar.auth_profiles.service_account.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.token_json' => $path,
            'google-calendar.calendar_id' => isset($setting['google_clender_id']) ? $setting['google_clender_id'] : '',
            'google-calendar.user_to_impersonate' => '',

        ]);
    }


    public static function getCalendarData($type)
    {
        Self::googleCalendarConfig();
        $data = Event::get();

        $type = Self::colorCodeData($type);
        $arrayJson = [];
        foreach ($data as $val) {
            $end_date = date_create($val->endDateTime);
            date_add($end_date, date_interval_create_from_date_string("1 days"));

            if ($val->colorId == "$type") {

                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->summary,
                    "start" => $val->startDateTime,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => Self::$colorCode[$type],
                    // "textColor" => '#FFF',
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }

    public static function addCalendarData($request, $type)
    {
        Self::googleCalendarConfig();

        $event = new Event();
        $event->name = $request->title;

        $event->startDateTime = Carbon::parse($request->start_date);
        $event->endDateTime = Carbon::parse($request->end_date);
        $event->colorId = Self::colorCodeData($type);

        $event->save();
    }
    public static function GetCacheSize()
    {
        $file_size = 0;
        foreach (\File::allFiles(storage_path('/framework')) as $file) {
            $file_size += $file->getSize();
        }
        $file_size = number_format($file_size / 1000000, 4);
        return $file_size;
    }
    public static function webhookSetting($module, $user_id = null)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
        } else {
            $user = \Auth::user();
        }
        $webhook = Webhook::where('module', $module)->where('created_by', '=', $user->id)->first();
        if (!empty($webhook)) {
            $url = $webhook->url;
            $method = $webhook->method;
            $reference_url  = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $data['method'] = $method;
            $data['reference_url'] = $reference_url;
            $data['url'] = $url;
            return $data;
        }
        return false;
    }
    public static function WebhookCall($url = null, $parameter = null, $method = '')
    {
        if (!empty($url) && !empty($parameter)) {
            try {
                $curlHandle = curl_init($url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
                $curlResponse = curl_exec($curlHandle);
                curl_close($curlHandle);
                if (empty($curlResponse)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }

    public function extraKeyword()
    {
        $keyArr = [
            __('Notification Template'),
        ];
    }

    public static function updateStorageLimit($company_id, $image_size)
    {
        $image_size = number_format($image_size / 1048576, 2);
        $user   = User::find($company_id);
        $plan   = Plan::find($user->plan);
        $total_storage = $user->storage_limit + $image_size;
        if ($plan->storage_limit <= $total_storage && $plan->storage_limit != -1) {
            $error = __('Plan storage limit is over so please upgrade the plan.');
            return $error;
        } else {
            $user->storage_limit = $total_storage;
        }
        $user->save();
        return 1;
    }

    public static function changeStorageLimit($company_id, $file_path)
    {
        $files =  \File::glob(storage_path($file_path));
        $fileSize = 0;
        foreach ($files as $file) {
            $fileSize += \File::size($file);
        }

        $image_size = number_format($fileSize / 1048576, 2);
        $user   = User::find($company_id);
        $plan   = Plan::find($user->plan);
        $total_storage = $user->storage_limit - $image_size;
        $user->storage_limit = $total_storage;
        $user->save();

        $status = false;
        foreach ($files as $key => $file) {
            if (\File::exists($file)) {
                $status = \File::delete($file);
            }
        }
        return true;
    }
    public static function flagOfCountry()
    {
        $arr = [
            'ar'    => '🇦🇪 ar',
            'da'    => '🇩🇰 da',
            'de'    => '🇩🇪 de',
            'es'    => '🇪🇸 es',
            'fr'    => '🇫🇷 fr',
            'it'    => '🇮🇹 it',
            'ja'    => '🇯🇵 ja',
            'nl'    => '🇳🇱 nl',
            'pl'    => '🇵🇱 pl',
            'ru'    => '🇷🇺 ru',
            'pt'    => '🇵🇹 pt',
            'en'    => '🇮🇳 en',
            'tr'    => '🇹🇷 tr',
            'pt-br' => '🇵🇹 pt-br',
            'zh'    =>  '🇨🇳 zh',
            'he'    =>  '🇮🇱 he'
        ];
        return $arr;
    }
    public static function flagOfCountryLogin()
    {
        $arr = [
            'Arabic'             => '🇦🇪',
            'Danish'             => '🇩🇰',
            'German'             => '🇩🇪',
            'Spanish'            => '🇪🇸 ',
            'French'             => '🇫🇷',
            'Italian'            => '🇮🇹',
            'Japanese'           => '🇯🇵',
            'Dutch'              => '🇳🇱',
            'Polish'             => '🇵🇱',
            'Russian'            => '🇷🇺',
            'Portuguese'         => '🇵🇹',
            'English'            => '🇮🇳',
            'Turkish'            => '🇹🇷',
            'Portuguese(Brazil)' => '🇵🇹 ',
            'Chinese'            => '🇨🇳',
            'Hebrew'             => '🇮🇱'
        ];
        return $arr;
    }
    public static function plansettings($user_id = null)
    {
        if (!empty($user_id)) {
            $user = User::where('id', $user_id)->first();
        } elseif (Auth::check()) {
            $user = Auth::user();
        }
        if ($user->type != 'owner') {
            $user = User::where('id', $user->created_by)->first();
        }
        $plansettings = [

            "enable_chatgpt" => 'off',
        ];

        if ($user != null && $user->plan) {
            $plan = Plan::where('id', $user->plan)->first();
            $plansettings = [
                "enable_chatgpt" => $plan->enable_chatgpt,
            ];
        }

        return $plansettings;
    }
    public static function languagecreate()
    {
        $languages = Utility::langList();
        foreach ($languages as $key => $lang) {
            $languageExist = Languages::where('code', $key)->first();
            if (empty($languageExist)) {
                $language = new Languages();
                $language->code = $key;
                $language->fullname = $lang;
                $language->save();
            }
        }
    }
    public static function langList()
    {
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Turkish",
            "pt-br" => "Portuguese(Brazil)",
        ];
        return $languages;
    }
    public static function langSetting()
    {
        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1)->get();
        if (count($data) == 0) {
            $data = DB::table('settings')->where('created_by', '=', 1)->get();
        }
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function getSMTPDetails($user_id)
    {
        $settings = Utility::settingsById($user_id);
        if ($settings) {
            config([
                'mail.default'                   => isset($settings['mail_driver'])       ? $settings['mail_driver']       : '',
                'mail.mailers.smtp.host'         => isset($settings['mail_host'])         ? $settings['mail_host']         : '',
                'mail.mailers.smtp.port'         => isset($settings['mail_port'])         ? $settings['mail_port']         : '',
                'mail.mailers.smtp.encryption'   => isset($settings['mail_encryption'])   ? $settings['mail_encryption']   : '',
                'mail.mailers.smtp.username'     => isset($settings['mail_username'])     ? $settings['mail_username']     : '',
                'mail.mailers.smtp.password'     => isset($settings['mail_password'])     ? $settings['mail_password']     : '',
                'mail.from.address'              => isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '',
                'mail.from.name'                 => isset($settings['mail_from_name'])    ? $settings['mail_from_name']    : '',
            ]);
            return $settings;
        } else {
            return redirect()->back()->with('Email SMTP settings does not configured so please contact to your site admin.');
        }
    }
    public static function getPusherSetting()
    {
        $settings = Utility::settings();
        if ($settings) {
            config([
                'chatify.pusher.key' => isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '',
                'chatify.pusher.secret' => isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '',
                'chatify.pusher.app_id' => isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '',
                'chatify.pusher.options.cluster' => isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '',
            ]);
            return $settings;
        }
    }

    public static function newLanguage($language)
    {
        $languages = EmailTemplateLang::where('lang', '=', 'en')->get();
        $templates = EmailTemplate::all();

        foreach ($templates as $template) {
            foreach ($languages as $emaillang) {
                if ($emaillang->parent_id == $template->id) {
                    $email = new EmailTemplateLang();
                    $email->parent_id  = $template->id;
                    $email->lang       = $language->code;
                    $email->subject    = $template->name;
                    $email->content    = $emaillang->content;
                    $email->save();
                }
            }
        }

        $notilanguages = NotificationTemplateLangs::where('lang', '=', 'en')->get();

        $notitemplates = NotificationTemplates::all();
        foreach ($notitemplates as  $notitemp) {
            foreach ($notilanguages as $notilang) {
                if ($notilang->parent_id == $notitemp->id) {
                    $notification = new NotificationTemplateLangs();
                    $notification->parent_id = $notitemp->id;
                    $notification->lang = $language->code;
                    $notification->content = $notilang->content;
                    $notification->variables = $notilang->variables;
                    $notification->created_by = \Auth::user()->creatorId();
                    $notification->save();
                }
            }
        }
    }

    public static function transaction($order, $company = '')
    {
        if ($company != '') {
            $objUser = $company;
        } else {
            $objUser = \Auth::user();
        }
        $referralSettings = ReferralSetting::first();
        $total = $order->price * $referralSettings->commission_per/ 100;
        $user_id = $order->user_id;
        $user = User::where('id', $user_id)->first();
        $refUser = User::where('referral_code', $user->referral_user)->first();
        $ids = ReferralTransaction::where('comapny_id', $objUser->id)->first();
        if ($objUser->referral_user != null && $ids == null && (isset($referralSettings) && $referralSettings->is_referral_enabled == 'on')) {
            $transaction = new ReferralTransaction();
            $transaction->comapny_id = $order->user_id;
            $transaction->plan_id    = $order->plan_id;
            $transaction->plan_price = $order->price;
            $transaction->commission = $referralSettings->commission_per;
            $transaction->amount     = $total;
            $transaction->referral_code = $user->referral_user;
            $transaction->ref_user_id = $refUser->id;
            $transaction->save();
        }
    }
}
