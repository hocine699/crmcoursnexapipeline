<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CommonCase;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Opportunities;
use App\Models\Plan;
use App\Models\Stream;
use App\Models\Utility;
use App\Models\User;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;


class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage Meeting')) {
            if (\Auth::user()->type == 'owner') {
                $meetings = Meeting::with('assign_user')->where('created_by', \Auth::user()->creatorId())->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'meeting';
                $defualtView->view   = 'list';
                User::userDefualtView($defualtView);
            } else {
                $meetings = Meeting::with('assign_user')->where('user_id', \Auth::user()->id)->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'meeting';
                $defualtView->view   = 'list';
            }

            return view('meeting.index', compact('meetings'));
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
        if (\Auth::user()->can('Create Meeting')) {
            $status            = Meeting::$status;
            $parent            = Meeting::$parent;
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_contact->prepend('--', 0);
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_lead->prepend('--', 0);
            $account_name      = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('meeting.create', compact('status', 'account_name', 'type', 'parent', 'user', 'attendees_contact', 'attendees_lead'));
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
        if (\Auth::user()->can('Create Meeting')) {
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
            $meeting                      = new Meeting();
            $meeting['user_id']           = $request->user;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['parent']            = $request->parent;
            $meeting['parent_id']         = $request->parent_id ?? '0';
            $meeting['account']           = $request->account;
            $meeting['description']       = $request->description;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['created_by']        = \Auth::user()->creatorId();
            $meeting->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );
            $Assign_user_phone = User::where('id', $request->user)->first();
            $setting  = Utility::settings(\Auth::user()->creatorId());

            $uArr = [
                // 'meeting_assign_user' => $request->name,
                'meeting_name' => $request->name,
                'meeting_start_date' => $request->start_date,
                'meeting_due_date' => $request->end_date,
                'meeting_description' => $request->description,
                'attendees_user' => $request->attendees_user,
                'attendees_contact' => $request->attendees_contact,

            ];
            $resp = Utility::sendEmailTemplate('meeting_assigned', [$meeting->id => $Assign_user_phone->email], $uArr);


            if (isset($setting['twilio_meeting_create']) && $setting['twilio_meeting_create'] == 1) {
                // $msg = "New Meeting" . " " . $request->name . " created by " . \Auth::user()->name . '.';
                $uArr = [
                    'meeting_name' => $request->name,
                    'meeting_start_date' => $request->start_date,
                    'meeting_due_date' => $request->end_date,
                    'user_name' => \Auth::user()->name,

                ];

                Utility::send_twilio_msg($Assign_user_phone->phone, 'new_meeting', $uArr);
            }
            if ($request->get('is_check')  == '1') {
                $type = 'meeting';
                $request1 = new Meeting();
                $request1->title = $request->name;
                $request1->start_date = $request->start_date;
                $request1->end_date = $request->end_date;
                Utility::addCalendarData($request1, $type);
            }
            //webhook
            $module = 'New Meeting';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($meeting);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status != true) {
                    $msg = "Webhook call failed.";
                }
            }
            if (\Auth::user()) {
                return redirect()->back()->with('success', __('Meeting successfully created!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                return redirect()->back()->with('error', __('Webhook call failed.') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        if (\Auth::user()->can('Show Meeting')) {
            $status = Meeting::$status;

            return view('meeting.view', compact('meeting', 'status'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {

        if (\Auth::user()->can('Edit Meeting')) {
            $status            = Meeting::$status;
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_contact->prepend('--', 0);
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $attendees_lead->prepend('--', 0);
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', '');
            $account_name      = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            // get previous user id
            $previous = Meeting::where('id', '<', $meeting->id)->max('id');
            // get next user id
            $next = Meeting::where('id', '>', $meeting->id)->min('id');

            // return view('meeting.edit', compact('meeting', 'account_name', 'attendees_contact', 'status', 'user', 'attendees_lead', 'previous', 'next'));
               return view('meeting.edit', compact('meeting', 'account_name', 'attendees_contact', 'status', 'user', 'attendees_lead', 'previous', 'next'))->with('start_date', $meeting->start_date)->with('end_date', $meeting->end_date);

        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        if (\Auth::user()->can('Edit Meeting')) {
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

            $meeting['user_id']           = $request->user_id;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['description']       = $request->description;
            $meeting['account']           = $request->account;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['created_by']        = \Auth::user()->creatorId();
            $meeting->update();


            Stream::create(
                [
                    'user_id' => \Auth::user()->id, 'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Meeting Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        if (\Auth::user()->can('Delete Meeting')) {
            $meeting->delete();

            return redirect()->back()->with('success', 'Meeting ' . $meeting->name . ' Deleted!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'meeting';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);
        return view('meeting.grid', compact('meetings'));
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

    public function get_meeting_data(Request $request)
    {
        $arrayJson = [];

        if ($request->get('calender_type') == 'goggle_calender') {
            $type = 'meeting';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            $data = Meeting::where('created_by', \Auth::user()->creatorId())->get();

            foreach ($data as $val) {

                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "url" => route('meeting.show', $val['id']),
                    "textColor" => '#FFF',
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }
}
