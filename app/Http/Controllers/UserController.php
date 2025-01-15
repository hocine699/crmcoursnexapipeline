<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Task;
use App\Models\User;
use App\Models\Plan;
use App\Models\Order;
use App\Models\UserDefualtView;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lab404\Impersonate\Impersonate;

class UserController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage User')) {
            if (\Auth::user()->type == 'super admin') {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            }
            if (\Auth::user()->type == 'owner') {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->get();
                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'user';
                $defualtView->view   = 'list';
                User::userDefualtView($defualtView);
            } else {
                $users = User::where('created_by', \Auth::user()->creatorId())->get();
                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'user';
                $defualtView->view   = 'list';
            }
            return view('user.index', compact('users'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create User')) {
            $setting  = Utility::settingsById(\Auth::user()->id);
            // 'lang' => (\Auth::user()->type == 'admin' ) ? Utility::getValByName('default_language') : $setting['default_owner_language'],
            $roles   =  Role::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $gender  =  User::$gender;
            $type    =  User::$type;

            return view('user.create', compact('gender', 'type', 'roles'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create User')) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
            if (\Auth::user()->type == 'super admin') {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $user               = new User();
                if (Utility::getValByName('verified_button') == "off") {
                    $user['username']   = $request->username;
                    $user['name']       = $request->name;
                    $user['email']      = $request->email;
                    // $user['email_verified_at'] = $request->email_verified_at;
                    $user['email_verified_at'] = date('H:i:s');
                    $user['password']   = Hash::make($request->password);
                    $user['type']       = 'owner';
                    $user['lang']       = !empty($default_language) ? $default_language->value : '';
                    $user['created_by'] = \Auth::user()->creatorId();
                    $user['plan']       = Plan::first()->id;
                    $user->save();
                    $role_r = Role::findByName('owner');
                    $user->assignRole($role_r);
                    $user->userDefaultDataRegister($user->id);
                } else {
                    $user['username']   = $request->username;
                    $user['name']       = $request->name;
                    $user['email']      = $request->email;
                    $user['email_verified_at'] = $request->email_verified_at;
                    // $user['email_verified_at'] = date('H:i:s');
                    $user['password']   = Hash::make($request->password);
                    $user['type']       = 'owner';
                    $user['lang']       = !empty($default_language) ? $default_language->value : '';
                    $user['created_by'] = \Auth::user()->creatorId();
                    $user['plan']       = Plan::first()->id;
                    $user->save();
                    $role_r = Role::findByName('owner');
                    $user->assignRole($role_r);
                    $user->userDefaultDataRegister($user->id);
                }

                $uArr = [
                    'email' => $user->email,
                    'password' => $request->password,
                ];

                $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);

                return redirect()->back()->with('success', __('User Successfully Inserted.'));
            } else {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'username' => 'required|max:120',
                        'name' => 'required|max:120',
                        'email' => [
                            'required',
                            Rule::unique('users')->where(function ($query) {
                                return $query->where('created_by', \Auth::user()->id);
                            })
                        ],
                        'password' => 'required|min:6',
                        'avatar' => 'image',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $setting  = Utility::settingsById(\Auth::user()->id);
                $objUser    = User::find(\Auth::user()->creatorId());
                $total_user = $objUser->countUsers();


                $plan       = Plan::find($objUser->plan);
                if ($total_user < $plan->max_user || $plan->max_user == -1) {
                    $role_r             = Role::findById($request->user_roles);
                    $user               = new User();
                    $user['username']   = $request->username;
                    $user['name']       = $request->name;
                    $user['title']      = $request->title;
                    $user['email']      = $request->email;
                    $user['email_verified_at'] = $request->email_verified_at;
                    $user['email_verified_at'] = date('H:i:s');
                    $user['phone']      = $request->phone;
                    $user['gender']     = $request->gender;
                    $user['is_active']  = ($request->is_active == 'on') ? 1 : 0;
                    $user['lang']       = !empty($setting['default_owner_language']) ? $setting['default_owner_language'] : 'en';
                    $user['type']       = $role_r->name;
                    $user['user_roles'] = $role_r->id;
                    $user['password']   = Hash::make($request->password);

                    if (!empty($request->avatar)) {
                        $image_size = $request->file('avatar')->getSize();
                        $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                        if ($result == 1) {
                            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
                            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            $extension       = $request->file('avatar')->getClientOriginalExtension();
                            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                            $dir        = 'upload/profile/';
                            $path = Utility::upload_file($request, 'avatar', $fileNameToStore, $dir, []);
                            $url = '';

                            $user['avatar']     = !empty($request->avatar) ? $fileNameToStore : '';
                            if ($path['flag'] == 1) {
                                $url = $path['url'];
                            } else {
                                return redirect()->back()->with('error', __($path['msg']));
                            }
                        }
                    }
                    $user['created_by'] = \Auth::user()->creatorId();
                    $user->save();

                    $user->assignRole($role_r);

                    Stream::create(
                        [
                            'user_id' => \Auth::user()->id,
                            'created_by' => \Auth::user()->creatorId(),
                            'log_type' => 'created',
                            'remark' => json_encode(
                                [
                                    'owner_name' => \Auth::user()->username,
                                    'title' => 'user',
                                    'stream_comment' => '',
                                    'user_name' => $request->name,
                                ]
                            ),
                        ]
                    );
                    $uArr = [
                        'email' => $user->email,
                        'password' => $request->password,
                    ];
                    $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $uArr);
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    if (isset($setting['twilio_user_create']) && $setting['twilio_user_create'] == 1) {
                        $uArr = [
                            'email' => $user->email,
                            'password' => $user->password,
                            'user_name'  => \Auth::user()->name,
                            'app_name' => env('APP_NAME'),
                            // 'app_url' => url('/'),
                        ];
                        Utility::send_twilio_msg($user->phone, 'new_user', $uArr);
                    }

                    //webhook
                    $module = 'New User';
                    $webhook =  Utility::webhookSetting($module, $user->created_by);
                    if ($webhook) {
                        $parameter = json_encode($user);
                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        if ($status != true) {
                            $msg = "Webhook call failed.";
                        }
                    }
                    if (\Auth::user()) {
                        return redirect()->back()->with('success', __('user successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')) . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
                    } else {
                        return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                    }
                    $uArr = [
                        'email' => $user->email,
                        'password' => $request->password,
                    ];


                    return redirect()->back()->with('success', __('User Successfully Inserted.' . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '')));
                } else {
                    return redirect()->back()->with('error', __('Your user limit is over, Please upgrade plan.'));
                }
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    public function show($id)
    {
        if (\Auth::user()->can('Show User')) {
            $user       = User::find($id);
            $user_roles = $user->user_roles;

            $roles      = Role::where('id', $user_roles)->get();
            return view('user.view', compact('user', 'roles'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('Edit User')) {
            $user = User::find($id);
            $gender = User::$gender;
            $type   = User::$type;
            $roles = Role::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $tasks = Task::where('user_id', $user->id)->get();

            // get previous user id
            $previous = User::where('id', '<', $user->id)->max('id');
            // get next user id
            $next = User::where('id', '>', $user->id)->min('id');


            $log_type = 'user comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            return view('user.edit', compact('user', 'gender', 'type', 'roles', 'tasks', 'streams', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('Edit User')) {
            $user      = User::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'username' => 'required|max:120',
                    'name' => 'required|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $role_r             = Role::findById($request->user_roles);
            $user['username']   = $request->username;
            $user['name']       = $request->name;
            $user['title']      = $request->title;
            $user['phone']      = $request->phone;
            $user['gender']     = $request->gender;
            $user['is_active']  = ($request->is_active == 'on') ? 1 : 0;
            $user['type']       = $role_r->name;
            $user['user_roles'] = $role_r->id;
            $user->update();

            $user->assignRole($role_r);
            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'user',
                            'stream_comment' => '',
                            'user_name' => $request->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('User Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function destroy($id)
    {

        if (\Auth::user()->can('Delete User')) {
            $user = User::find($id);

            if ($user) {
                if (\Auth::user()->type == 'super admin') {

                    $users = User::where('created_by', $id)->delete();

                    $user->delete();

                    return redirect()->back()->with('success', __('Company Successfully deleted'));
                }
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
            $user->delete();

            return redirect()->back()->with('success', __('User Successfully Deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    public function grid()
    {

        if (\Auth::user()->can('Manage User')) {
            if (\Auth::user()->type == 'owner' || \Auth::user()->type == 'Manager') {
                $users = User::where('created_by', \Auth::user()->creatorId())->get();
            } else {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            }

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'user';
            $defualtView->view   = 'grid';

            User::userDefualtView($defualtView);
            return view('user.grid', compact('users'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function profile()
    {
        $userDetail = \Auth::user();

        return view('user.profile', compact('userDetail'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();

        $user = User::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                'profile' => 'image',
            ]
        );

        if ($request->hasFile('profile')) {

            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir = 'upload/profile/';
            $image_path = $dir . $userDetail['avatar'];
            if (\File::exists($image_path)) {
                \File::delete($image_path);
            }
            $url = '';
            $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $url = $path['url'];
            } else {
                return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
            }
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();

        return redirect()->back()->with('success', 'Profile successfully updated.');
    }

    public function updatePassword(Request $request)
    {
        if (\Auth::Check()) {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = \Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['current_password'], $current_password)) {
                $user_id            = \Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            } else {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }

    public function changeMode()
    {
        $usr = Auth::user();
        if ($usr->mode == 'light') {
            $usr->mode      = 'dark';
            $usr->dark_mode = 1;
        } else {
            $usr->mode      = 'light';
            $usr->dark_mode = 0;
        }
        $usr->save();
        return redirect()->back();
    }

    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);

        $plans = Plan::get();

        return view('user.plan', compact('user', 'plans'));
    }

    public function activePlan($user_id, $plan_id)
    {
        $user       = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id);
        $plan       = Plan::find($plan_id);
        if ($plan->status == 0) {
            return redirect()->route('user.index')->with('error', 'Plan is Disabed,please enable the plan.');
        }
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => Utility::getValByName('site_currency'),
                    'txn_id' => '',
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'payment_type' => __('Manually'),
                    'user_id' => $user->id,
                ]
            );

            return redirect()->route('user.index')->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }
    }

    public function employeePassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);



        return view('user.reset', compact('user'));
    }

    public function employeePasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('user.index')->with(
            'success',
            'User Password successfully updated.'
        );
    }

    public function loginWithCompany(Request $request, User $user,  $id)
    {
        $user =    User::find($id);

        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect('/dashboard');
        }
    }

    public function exitAdmin(Request $request)
    {
        Auth::user()->leaveImpersonation($request->user());
        return redirect('/dashboard');
    }
    public function companyInfo(Request $request, $id)
    {
        $userData = User::where('created_by', $id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        return view('user.companyinfo', compact('userData', 'id'));
    }


    public function userUnable(Request $request)
    {
        User::where('id', $request->id)->update(['is_disable' => $request->is_disable]);
        $userData = User::where('created_by', $request->company_id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        if ($request->is_disable == 1) {

            return response()->json(['success' => __('User successfully unable.'), 'userData' => $userData]);
        } else {
            return response()->json(['success' => __('User successfully disable.'), 'userData' => $userData]);
        }
    }

    public function LoginManage($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);

        if ($user->is_enable_login == 1) {
            $user->is_enable_login = 0;
            $user->save();
            return redirect()->back()->with('success', __('User login disable successfully.'));
        } else {
            $user->is_enable_login = 1;
            $user->save();
            return redirect()->back()->with('success', __('User login enable successfully.'));
        }

    }
}
