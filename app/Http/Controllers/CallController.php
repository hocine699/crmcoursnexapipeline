<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Stream;
use App\Models\UserDefualtView;
use DemeterChain\C;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Account;
use App\Models\Utility;
use App\Models\Opportunities;
use App\Models\CommonCase;
use App\Models\Plan;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage Call')) {
            if (\Auth::user()->type == 'owner') {
                $calls = Call::with('assign_user')->where('created_by', \Auth::user()->creatorId())->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'call';
                $defualtView->view   = 'list';
                User::userDefualtView($defualtView);
            } else {

                $calls = Call::with('assign_user')->where('user_id', \Auth::user()->id)->get();
                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'call';
                $defualtView->view   = 'list';
            }

            return view('call.index', compact('calls'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $id)
    {
        if (\Auth::user()->can('Create Call')) {
            $status            = Call::$status;
            $direction         = Call::$direction;
            $parent            = Call::$parent;
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_contact->prepend('--', 0);
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_lead->prepend('--', 0);
            $account_name      = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('call.create', compact('status', 'type', 'account_name', 'parent', 'user', 'attendees_contact', 'attendees_lead', 'direction'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Call')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $call                      = new Call();
            $call['user_id']           = $request->user;
            $call['name']              = $request->name;
            $call['status']            = $request->status;
            $call['direction']         = $request->direction;
            $call['start_date']        = $request->start_date;
            $call['end_date']          = $request->end_date;
            $call['parent']            = $request->parent;
            $call['parent_id']         = $request->parent_id ?? '0';
            $call['account']           = $request->account;
            $call['description']       = $request->description;
            $call['attendees_user']    = $request->attendees_user;
            $call['attendees_contact'] = $request->attendees_contact;
            $call['attendees_lead']    = $request->attendees_lead;
            $call['created_by']        = \Auth::user()->creatorId();
            $call->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );
            if ($request->get('is_check')  == '1') {
                $type = 'call';
                $request1 = new Call();
                $request1->title = $request->name;
                $request1->start_date = $request->start_date;
                $request1->end_date = $request->end_date;
                Utility::addCalendarData($request1, $type);
            }

            return redirect()->back()->with('success', __('Call Successfully Created.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Call $call)
    {
        if (\Auth::user()->can('Show Call')) {
            $status = Call::$status;

            return view('call.view', compact('call', 'status'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Call $call)
    {
        if (\Auth::user()->can('Edit Call')) {
            $status            = Call::$status;
            $direction         = Call::$direction;
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_contact->prepend('--', 0);
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_lead->prepend('--', 0);
            $account_name  = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);

            // get previous user id
            $previous = Call::where('id', '<', $call->id)->max('id');
            // get next user id
            $next = Call::where('id', '>', $call->id)->min('id');

            return view('call.edit', compact('call', 'account_name', 'attendees_contact', 'status', 'user', 'attendees_lead', 'direction', 'previous', 'next'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Call $call)
    {
        if (\Auth::user()->can('Edit Call')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $call['user_id']           = $request->user_id;
            $call['name']              = $request->name;
            $call['status']            = $request->status;
            $call['direction']         = $request->direction;
            $call['start_date']        = $request->start_date;
            $call['end_date']          = $request->end_date;
            $call['description']       = $request->description;
            $call['account']           = $request->account;
            $call['attendees_user']    = $request->attendees_user;
            $call['attendees_contact'] = $request->attendees_contact;
            $call['attendees_lead']    = $request->attendees_lead;
            $call['created_by']        = \Auth::user()->creatorId();
            $call->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Call Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Call $call)
    {
        if (\Auth::user()->can('Delete Call')) {
            $call->delete();

            return redirect()->back()->with('success', 'Call ' . $call->name . ' Deleted!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $calls = Call::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'call';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('call.grid', compact('calls'));
    }

    public function getparent(Request $request)
    {
        if ($request->parent == 'account') {
            $parent = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'lead') {
            $parent = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'contact') {
            $parent = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'opportunities') {
            $parent = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'case') {
            $parent = CommonCase::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $parent = '';
        }

        return response()->json($parent);
    }
    public function get_call_data(Request $request)
    {
        $arrayJson = [];
        if ($request->get('calender_type') == 'goggle_calender') {

            $type = 'call';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            $data = call::where('created_by', \Auth::user()->creatorId())->get();
            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "url" => route('call.show', $val['id']),
                    "textColor" => '#FFF',
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }
}
