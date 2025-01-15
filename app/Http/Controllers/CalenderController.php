<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Utility;

use Illuminate\Http\Request;

class CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transdate = date('Y-m-d', time());
        if(\Auth::user()->type == 'owner'){
            $calls    = Call::where('created_by', \Auth::user()->creatorId())->get();
            $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();
            $tasks    = Task::where('created_by', \Auth::user()->creatorId())->get();
        }
        else
        {
            $calls    = Call::where('user_id', \Auth::user()->id)->get();
            $meetings = Meeting::where('user_id', \Auth::user()->id)->get();
            $tasks    = Task::where('user_id', \Auth::user()->id)->get();
        }
        return view('calendar.index', compact('transdate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function calendar(){

    }

    public function get_data(Request $request){

            $arrMeeting = [];
            $arrTask    = [];
            $arrCall    = [];

        if($request->get('calender_type') == 'goggle_calender')
        {
            if($type ='task'){
            $arrTask =  Utility::getCalendarData($type);
            }

            if($type ='meeting'){
            $arrMeeting =  Utility::getCalendarData($type);
            }

            if($type = 'call'){
            $arrCall =  Utility::getCalendarData($type);
            }

             $arrayJson = array_merge($arrCall, $arrMeeting, $arrTask);

        }else{

            $arrMeeting = [];
            $arrTask    = [];
            $arrCall    = [];

            if(\Auth::user()->type == 'owner'){
                $calls    = Call::where('created_by', \Auth::user()->creatorId())->get();
                $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();
                $tasks    = Task::where('created_by', \Auth::user()->creatorId())->get();
            }
            else
            {
                $calls    = Call::where('user_id', \Auth::user()->id)->get();
                $meetings = Meeting::where('user_id', \Auth::user()->id)->get();
                $tasks    = Task::where('user_id', \Auth::user()->id)->get();
            }

        foreach($tasks as $val)

                {

                    $end_date=date_create($val->end_date);
                    date_add($end_date,date_interval_create_from_date_string("1 days"));
                    $arrTask[] = [
                        "id"=> $val->id,
                        "title" => $val->name,
                        "start" => $val->start_date,
                        "end" => date_format($end_date,"Y-m-d H:i:s"),
                        "className" => $val->color,
                        "textColor" => '#FFF',
                        "url" => route('task.show', $val['id']),
                        "allDay" => true,
                    ];
                }
                foreach($meetings as $val)
                {

                    $end_date=date_create($val->end_date);
                    date_add($end_date,date_interval_create_from_date_string("1 days"));
                    $arrMeeting[] = [
                        "id"=> $val->id,
                        "title" => $val->name,
                        "start" => $val->start_date,
                        "end" => date_format($end_date,"Y-m-d H:i:s"),
                        "className" => $val->color,
                        "textColor" => '#FFF',
                        "url" => route('meeting.show', $val['id']),
                        "allDay" => true,
                    ];
                }
                foreach($calls as $val)
                {

                    $end_date=date_create($val->end_date);
                    date_add($end_date,date_interval_create_from_date_string("1 days"));
                    $arrCall[] = [
                        "id"=> $val->id,
                        "title" => $val->name,
                        "start" => $val->start_date,
                        "end" => date_format($end_date,"Y-m-d H:i:s"),
                        "className" => $val->color,
                        "textColor" => '#FFF',
                        "url" => route('call.show', $val['id']),
                        "allDay" => true,
                    ];
                }
                        $arrayJson = array_merge($arrCall, $arrMeeting, $arrTask);


                }
                return $arrayJson;
    }
}
