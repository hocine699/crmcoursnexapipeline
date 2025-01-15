<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Call;
use App\Models\CommonCase;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Opportunities;
use App\Models\Product;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Models\Task;
use App\Models\User;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\LandingPageSections;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use function Symfony\Component\String\s;

class DashboardController extends Controller
{

    public function index()
    {
        if (\Auth::check()) {
            if (\Auth::user()->type == 'super admin') {
                $user                       = \Auth::user();
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '-');
                $chartData                  = $this->getOrderChart(['duration' => 'week']);

                return view('super_admin', compact('user', 'chartData'));
            } else {
                $data['totalUser']          = User::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalAccount']       = Account::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalContact']       = Contact::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalLead']          = Lead::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalSalesorder']    = $totalSalesOrder = SalesOrder::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalInvoice']       = $totalInvoice = Invoice::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalQuote']         = $totalQuote = Quote::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalOpportunities'] = Opportunities::where('created_by', \Auth::user()->creatorId())->count();
                $data['totalProduct']       = Product::where('created_by', \Auth::user()->creatorId())->count();
                $data['invoiceColor']       = Invoice::$statuesColor;


                $statuss  = Invoice::$status;
                $invoices = [];
                foreach ($statuss as $id => $status) {
                    $invoice                   = $total = Invoice::where('status', $id)->where('created_by', \Auth::user()->creatorId())->count();
                    $percentage                = ($totalInvoice != 0) ? ($total * 100) / $totalInvoice : '0';
                    $invoicedata['percentage'] = number_format($percentage, 2);
                    $invoicedata['data']       = $invoice;
                    $invoicedata['status']     = $status;
                    $invoices[]                = $invoicedata;
                }

                $data['invoice'] = $invoices;


                $statuss = Quote::$status;
                $quotes  = [];
                foreach ($statuss as $id => $status) {
                    $quote = $total = Quote::where('status', $id)->where('created_by', \Auth::user()->creatorId())->count();

                    $percentage              = ($totalQuote != 0) ? ($total * 100) / $totalQuote : '0';
                    $quotedata['percentage'] = number_format($percentage, 2);
                    $quotedata['data']       = $quote;
                    $quotedata['status']     = $status;
                    $quotes[]                = $quotedata;
                }
                $data['quote'] = $quotes;


                $statuss     = SalesOrder::$status;
                $salesOrders = [];
                foreach ($statuss as $id => $status) {
                    $salesorder                   = SalesOrder::where('status', $id)->where('created_by', \Auth::user()->creatorId())->count();
                    $percentage                   = ($totalSalesOrder != 0) ? ($total * 100) / $totalSalesOrder : '0';
                    $salesorderdata['percentage'] = number_format($percentage, 2);
                    $salesorderdata['data']       = $salesorder;
                    $salesorderdata['status']     = $status;
                    $salesOrders[]                = $salesorderdata;
                }
                $data['salesOrder'] = $salesOrders;

                $data['lineChartData'] = \Auth::user()->getIncExpLineChartDate();

                $data['calendar'] = $this->calendarData();

                $data['topDueTask']         = Task::select('*')->where('created_by', \Auth::user()->creatorId())->where('due_date', '<', date('Y-m-d'))->limit(5)->get();
                $data['topMeeting']         = Meeting::where('created_by', \Auth::user()->creatorId())->where('start_date', '>', date('Y-m-d'))->limit(5)->get();
                $data['thisMonthCall']      = Call::whereBetween(
                    'start_date',
                    [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]
                )->where('created_by', \Auth::user()->creatorId())->limit(5)->get();

                $users = User::find(\Auth::user()->creatorId());
                $plan = Plan::find($users->plan);
                if (isset($plan->storage_limit) ? $plan->storage_limit > 0 : '') {
                    $storage_limit = ($users->storage_limit / $plan->storage_limit) * 100;
                } else {
                    $storage_limit = 0;
                }
                return view('home', compact('data','users','plan','storage_limit'));
            }
        } else {

            if (!file_exists(storage_path() . "/installed")) {
                header('location:install');
                die;
            } else {
                $settings = Utility::settings();
                if ($settings['display_landing_page'] == 'on' && \Schema::hasTable('landing_page_settings')) {
                    $plans = Plan::get();

                    $get_section = LandingPageSections::orderBy('section_order', 'ASC')->get();

                    return view('landingpage::layouts.landingpage', compact('plans', 'get_section'));
                } else {
                    return redirect('login');
                }
            }
        }
    }
    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");
                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach ($arrDuration as $date => $label) {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    public  function calendarData($calenderdata = 'all')
    {
        $calls    = Call::where('created_by', \Auth::user()->creatorId())->get();
        $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();
        $tasks    = Task::where('created_by', \Auth::user()->creatorId())->get();

        $arrMeeting = [];
        $arrTask    = [];
        $arrCall    = [];

        if ($calenderdata == 'call') {
            foreach ($calls as $call) {
                $arr['id']        = $call['id'];
                $arr['title']     = $call['name'];
                $arr['start']     = $call['start_date'];
                $arr['end']       = $call['end_date'];
                $arr['className'] = 'event-primary';
                $arr['url']       = route('call.show', $call['id']);
                $arrCall[]        = $arr;
            }
        } elseif ($calenderdata == 'task') {
            foreach ($tasks as $task) {
                $arr['id']        = $task['id'];
                $arr['title']     = $task['name'];
                $arr['start']     = $task['start_date'];
                $arr['end']       = $task['due_date'];
                $arr['className'] = 'event-success';
                $arr['url']       = route('task.show', $task['id']);
                $arrTask[]        = $arr;
            }
        } elseif ($calenderdata == 'meeting') {
            foreach ($meetings as $meeting) {
                $arr['id']        = $meeting['id'];
                $arr['title']     = $meeting['name'];
                $arr['start']     = $meeting['start_date'];
                $arr['end']       = $meeting['end_date'];
                $arr['className'] = 'event-info';
                $arr['url']       = route('meeting.show', $meeting['id']);
                $arrMeeting[]     = $arr;
            }
        } else {
            foreach ($calls as $call) {
                $arr['id']        = $call['id'];
                $arr['title']     = $call['name'];
                $arr['start']     = $call['start_date'];
                $arr['end']       = $call['end_date'];
                $arr['className'] = 'event-primary';
                $arr['url']       = route('call.show', $call['id']);
                $arrCall[]        = $arr;
            }
            foreach ($tasks as $task) {
                $arr['id']        = $task['id'];
                $arr['title']     = $task['name'];
                $arr['start']     = $task['start_date'];
                $arr['end']       = $task['due_date'];
                $arr['className'] = 'event-success';
                $arr['url']       = route('task.show', $task['id']);
                $arrTask[]        = $arr;
            }
            foreach ($meetings as $meeting) {
                $arr['id']        = $meeting['id'];
                $arr['title']     = $meeting['name'];
                $arr['start']     = $meeting['start_date'];
                $arr['end']       = $meeting['end_date'];
                $arr['className'] = 'event-info';
                $arr['url']       = route('meeting.show', $meeting['id']);
                $arrMeeting[]     = $arr;
            }
        }

        $calandar = array_merge($arrCall, $arrMeeting, $arrTask);
        return  str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));
    }
    public function get_data(Request $request)
    {
        $arrMeeting = [];
        $arrTask    = [];
        $arrCall    = [];

        if ($request->get('calender_type') == 'goggle_calender') {
            if ($type = 'task') {
                $arrTask =  Utility::getCalendarData($type);
            }

            if ($type = 'meeting') {
                $arrMeeting =  Utility::getCalendarData($type);
            }

            if ($type = 'call') {
                $arrCall =  Utility::getCalendarData($type);
            }

            $arrayJson = array_merge($arrCall, $arrMeeting, $arrTask);
        } else {

            $arrMeeting = [];
            $arrTask    = [];
            $arrCall    = [];

            $calls    = Call::get();
            $meetings = Meeting::get();
            $tasks    = Task::get();



            foreach ($tasks as $val) {

                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrTask[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "url" => route('task.show', $val['id']),
                    "allDay" => true,
                ];
            }
            foreach ($meetings as $val) {

                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrMeeting[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "url" => route('meeting.show', $val['id']),
                    "allDay" => true,
                ];
            }
            foreach ($calls as $val) {

                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrCall[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
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
