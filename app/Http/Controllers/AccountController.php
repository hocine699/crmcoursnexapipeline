<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CommonCase;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Opportunities;
use App\Models\Stream;
use App\Models\Task;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use App\Models\AccountIndustry;
use App\Models\AccountType;
use App\Models\Call;
use App\Models\Invoice;
use App\Models\Meeting;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Exception\DceSecurityException;
use Spatie\Permission\Models\Role;

class AccountController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Account')) {
            if (\Auth::user()->type == 'owner') {
                $accounts = Account::with('assign_user')->where('created_by', \Auth::user()->creatorId())->get();
                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'account';
                $defualtView->view   = 'list';

                User::userDefualtView($defualtView);
            } else {
                $accounts = Account::with('assign_user')->where('user_id', \Auth::user()->id)->get();
                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'account';
                $defualtView->view   = 'list';
            }
            return view('account.index', compact('accounts'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Account')) {
            $accountype  = accountType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $industry    = accountIndustry::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $document_id = Document::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $document_id->prepend('--', '0');
            $user        = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--','0');
            return view('account.create', compact('accountype', 'industry', 'user', 'document_id'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|unique:accounts',
                    'type' => 'required',
                    'industry' => 'required',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $objUser    = \Auth::user();
            $total_account = $objUser->countAccounts();
            $plan       = Plan::find($objUser->plan);
            if ($plan == null) {
                $user = User::where('id', $objUser->created_by)->first();
                $plan = Plan::where('id', $user->plan)->first();
            }

            if ($total_account < $plan->max_account || $plan->max_account == -1 || $plan->max_account <> null) {
                $account                        = new account();
                $account['user_id']             = $request->user;
                $account['document_id']         = $request->document_id;
                $account['name']                = $request->name;
                $account['email']               = $request->email;
                $account['phone']               = $request->phone;
                $account['website']             = $request->website;
                $account['billing_address']     = $request->billing_address;
                $account['billing_city']        = $request->billing_city;
                $account['billing_state']       = $request->billing_state;
                $account['billing_country']     = $request->billing_country;
                $account['billing_postalcode']  = $request->billing_postalcode;
                $account['shipping_address']    = $request->shipping_address;
                $account['shipping_city']       = $request->shipping_city;
                $account['shipping_state']      = $request->shipping_state;
                $account['shipping_country']    = $request->shipping_country;
                $account['shipping_postalcode'] = $request->shipping_postalcode;
                $account['type']                = $request->type;
                $account['industry']            = $request->industry;
                $account['description']         = $request->description;
                $account['created_by']          = \Auth::user()->creatorId();
                $account->save();
                Stream::create(
                    [
                        'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                        'log_type' => 'created',
                        'remark' => json_encode(
                            [
                                'owner_name' => \Auth::user()->username,
                                'title' => 'account',
                                'stream_comment' => '',
                                'user_name' => $account->name,
                            ]
                        ),
                    ]
                );
                return redirect()->back()->with('success', __('Account Successfully Created.'));
            } else {
                return redirect()->back()->with('error', __('Your account limit is over, Please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function show(account $account)
    {
        if (\Auth::user()->can('Show Account')) {
            return view('account.view', compact('account'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function edit(account $account)
    {

        if (\Auth::user()->can('Edit Account')) {
            $accountype     = accountType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $industry       = accountIndustry::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user           = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '0');
            $contacts       = Contact::where('account', $account->id)->get();
            // $contacts = Contact::where('created_by', \Auth::user()->creatorId())->get();
            $opportunitiess = Opportunities::where('account', $account->id)->get();
            $cases          = CommonCase::where('account', $account->id)->get();
            $documents      = Document::where('account', $account->id)->get();
            $document_id    = Document::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $document_id->prepend('--', '0');
            $quotes         = Quote::where('account', $account->id)->get();
            $salesorders    = SalesOrder::where('account', $account->id)->get();
            $salesinvoices  = Invoice::where('account', $account->id)->get();
            $calls          = Call::where('account', $account->id)->get();
            $meetings       = Meeting::where('account', $account->id)->get();

            // get previous user id
            $previous = Account::where('id', '<', $account->id)->max('id');
            // get next user id
            $next = Account::where('id', '>', $account->id)->min('id');

            $parent   = 'account';
            $tasks    = Task::where('parent', $parent)->where('parent_id', $account->id)->get();
            $log_type = 'account comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            return view('account.edit', compact('account','meetings','calls','salesorders','salesinvoices','quotes', 'accountype', 'industry', 'user', 'contacts', 'opportunitiess', 'tasks', 'cases', 'documents', 'streams', 'document_id', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function update(Request $request, account $account)
    {
        if (\Auth::user()->can('Edit Account')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|unique:users',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $account['user_id']             = $request->user;
            $account['document_id']         = $request->document_id;
            $account['name']                = $request->name;
            $account['email']               = $request->email;
            $account['phone']               = $request->phone;
            $account['website']             = $request->website;
            $account['billing_address']     = $request->billing_address;
            $account['billing_city']        = $request->billing_city;
            $account['billing_state']       = $request->billing_state;
            $account['billing_country']     = $request->billing_country;
            $account['billing_postalcode']  = $request->billing_postalcode;
            $account['shipping_address']    = $request->shipping_address;
            $account['shipping_city']       = $request->shipping_city;
            $account['shipping_state']      = $request->shipping_state;
            $account['shipping_country']    = $request->shipping_country;
            $account['shipping_postalcode'] = $request->shipping_postalcode;
            $account['type']                = $request->type;
            $account['industry']            = $request->industry;
            $account['description']         = $request->description;
            $account['created_by']          = \Auth::user()->creatorId();
            $account->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'account',
                            'stream_comment' => '',
                            'user_name' => $account->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Account Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function destroy(account $account)
    {
        if (\Auth::user()->can('Delete Account')) {
            $account->delete();

            return redirect()->back()->with('success', __('User successfully deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        if (\Auth::user()->type == 'owner') {
            $accounts = Account::where('created_by', \Auth::user()->creatorId())->get();
        } else {
            $accounts = Account::where('user_id', \Auth::user()->id)->get();
        }

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'account';
        $defualtView->view   = 'grid';

        User::userDefualtView($defualtView);
        return view('account.grid', compact('accounts'));
    }
}
