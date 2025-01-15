<?php

namespace App\Models;

use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;
    use Impersonate;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'title',
        'email',
        'email_verified_at',
        'email_verified_at',
        'phone',
        'gender',
        'type',
        'is_active',
        'is_enable_login',
        'roles',
        'password',
        'avatar',
        'plan',
        'plan_expire_date',
        'trial_expire_date',
        'is_trial_done',
        'created_by',
        'referral_code',
        'referral_user',

    ];
    protected $appends  = [
        'type_name',
        'user_roles_name',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected     $casts  = [
        'email_verified_at' => 'datetime',
    ];
    public static $gender = [
        'male' => 'Male',
        'female' => 'Female',
    ];
    public static $type   = [
        'admin' => 'Admin',
        'regular' => 'Regular',
    ];


    public function creatorId()
    {
        if ($this->type == 'owner' || $this->type == 'super admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public function createId()
    {
        if ($this->type == 'super admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public static function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }

    public static function dateFormat($date)
    {

        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function getTypeNameAttribute()
    {
        if (!empty($this->type)) {
            $type = $this->type;

            return $this->attributes['type_name'] = $type;
        } else {
            return $this->attributes['type_name'] = '';
        }
    }

    public function getUserRolesNameAttribute()
    {
        $user_roles = User::find($this->user_roles);

        return $this->attributes['userrole_name'] = !empty($user_roles) ? $user_roles->name : '';
    }

    public static function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public function quoteNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["quote_prefix"] . sprintf("%05d", $number);
    }

    public function salesorderNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["salesorder_prefix"] . sprintf("%05d", $number);
    }

    public function stages()
    {
        return $this->hasOne('App\Models\TaskStage', 'id', 'stage');
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function getIncExpLineChartDate()
    {
        $usr           = \Auth::user();
        $m             = date("m");
        $de            = date("d");
        $y             = date("Y");
        $format        = 'Y-m-d';
        $arrDate       = [];
        $arrDateFormat = [];

        for ($i = 0; $i <= 15 - 1; $i++) {
            $date = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));

            $arrDay[]        = date('D', mktime(0, 0, 0, $m, ($de - $i), $y));
            $arrDate[]       = $date;
            $arrDateFormat[] = date("d", strtotime($date)) . '-' . __(date("M", strtotime($date)));
        }
        $data['day'] = $arrDateFormat;

        for ($i = 0; $i < count($arrDate); $i++) {
            $daysQuotes = Quote::select('*')->where('created_by', \Auth::user()->creatorId())->whereRAW('date_quoted = ?', $arrDate[$i])->get();
            $quoteArray = array();
            foreach ($daysQuotes as $daysQuote) {
                $quoteArray[] = $daysQuote->getTotal();
            }
            $quoteamount = number_format(!empty($quoteArray) ? array_sum($quoteArray) : 0, 2);
            $quateData[] = str_replace(',', '', $quoteamount);


            $daysInvoices = Invoice::select('*')->where('created_by', \Auth::user()->creatorId())->whereRAW('date_quoted = ?', $arrDate[$i])->get();
            $invoiceArray = array();
            foreach ($daysInvoices as $daysInvoice) {
                $invoiceArray[] = $daysInvoice->getTotal();
            }
            $invoiceamount = number_format(!empty($invoiceArray) ? array_sum($invoiceArray) : 0, 2);
            $invoiceData[] = str_replace(',', '', $invoiceamount);

            $daysSalesOrders = SalesOrder::select('*')->where('created_by', \Auth::user()->creatorId())->whereRAW('date_quoted = ?', $arrDate[$i])->get();
            $salesOrderArray = array();
            foreach ($daysSalesOrders as $daysSalesOrder) {
                $salesOrderArray[] = $daysSalesOrder->getTotal();
            }
            $salesorderamount = number_format(!empty($salesOrderArray) ? array_sum($salesOrderArray) : 0, 2);
            $salesOrderData[] = str_replace(',', '', $salesorderamount);
        }
        $data['invoiceAmount']    = $invoiceData;
        $data['quoteAmount']      = $quateData;
        $data['salesorderAmount'] = $salesOrderData;

        return $data;
    }

    public function countCompany()
    {
        return User::where('type', '=', 'owner')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'owner')->whereNotIn(
            'plan',
            [
                0,
                1,
            ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function assignPlan($planID,$duration = null,$user_id = null)
    {


        $plan = Plan::find($planID);
        if ($plan) {
            $this->plan = $plan->id;

            if ($plan->duration == 'month') {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->duration == 'year') {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            } else if ($plan->duration == 'lifetime') {
                $this->plan_expire_date = null;
                $this->trial_expire_date = null;
            }

            $this->save();

            $users    = User::where('created_by', '=', \Auth::user()->creatorId())->get();
            $accounts = Account::where('created_by', '=', \Auth::user()->creatorId())->get();
            $contacts = Contact::where('created_by', '=', \Auth::user()->creatorId())->get();


            if ($plan->max_user == -1) {
                foreach ($users as $user) {
                    $user->plan_is_active = 1;
                    $user->save();
                }
            } else {
                $userCount = 0;
                foreach ($users as $user) {
                    $userCount++;
                    if ($userCount <= $plan->max_user) {
                        $user->plan_is_active = 1;
                        $user->save();
                    } else {
                        $user->plan_is_active = 0;
                        $user->save();
                    }
                }
            }

            if ($plan->max_account == -1) {
                foreach ($accounts as $account) {
                    $account->is_active = 1;
                    $account->save();
                }
            } else {
                $accountCount = 0;
                foreach ($accounts as $account) {
                    $accountCount++;
                    if ($accountCount <= $plan->max_account) {
                        $account->is_active = 1;
                        $account->save();
                    } else {
                        $account->is_active = 0;
                        $account->save();
                    }
                }
            }

            if ($plan->max_contact == -1) {
                foreach ($contacts as $contact) {
                    $contact->is_active = 1;
                    $contact->save();
                }
            } else {
                $contactCount = 0;
                foreach ($contacts as $contact) {
                    $contactCount++;
                    if ($contactCount <= $plan->max_contact) {
                        $contact->is_active = 1;
                        $contact->save();
                    } else {
                        $contact->is_active = 0;
                        $contact->save();
                    }
                }
            }
            if($user_id != null)
            {
                $user = User::find($user_id);
            }
            else
            {
                $user =  User::find(\Auth::user()->id);
            }

            if($duration == 'Trial'){
                $user->trial_expire_date =  Carbon::now()->addDays($plan->trial_days)->isoFormat('YYYY-MM-DD');
                if($user->plan_expire_date)
                {
                    $user->plan_expire_date = null;
                }
            }
            $user->save();

            return ['is_success' => true];
        } else {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function countUser($id)
    {
      
        return User::where('created_by', $id)->count();

    }

    public function countAccount($id)
    {
        return Account::where('created_by', $id)->count();
    }

    public function countContact($id)
    {
        return Contact::where('created_by', $id)->count();
    }

    public function countUsers()
    {
        return User::where('type', '!=', 'super admin')->where('type', '!=', 'owner')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countAccounts()
    {
        return Account::where('created_by', '=', $this->creatorId())->count();
    }

    public function countContacts()
    {
        return Contact::where('created_by', '=', $this->creatorId())->count();
    }

    public static function userDefualtView($request)
    {

        $userId      = \Auth::user()->id;
        $defaultView = UserDefualtView::where('module', $request->module)->where('user_id', $userId)->first();

        if (empty($defaultView)) {
            $userView = new UserDefualtView();
        } else {
            $userView = $defaultView;
        }

        $userView->module  = $request->module;
        $userView->route   = $request->route;
        $userView->view    = $request->view;
        $userView->user_id = $userId;
        $userView->save();
    }

    public static function getDefualtViewRouteByModule($module)
    {
        $userId      = \Auth::user()->id;
        $defaultView = UserDefualtView::select('route')->where('module', $module)->where('user_id', $userId)->first();

        return !empty($defaultView) ? $defaultView->route : '';
    }

    public function currentPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan');
    }

    public function barcodeFormat()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_format']) ? $settings['barcode_format'] : 'code128';
    }

    public function barcodeType()
    {
        $settings = Utility::settings();
        return isset($settings['barcode_type']) ? $settings['barcode_type'] : 'css';
    }


    public static function getIdByUser($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function contractNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["contract_prefix"] . sprintf("%05d", $number);
    }


    public static function userDefaultDataRegister($user_id)
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();

        foreach ($allEmail as $email) {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => $user_id,
                    'is_active' => 0,
                ]
            );
        }
    }
}
