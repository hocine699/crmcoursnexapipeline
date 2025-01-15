<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\CommonCase;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Opportunities;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Report;
use App\Models\SalesOrder;
use App\Models\Stream;
use App\Models\Task;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Parent_;
use PhpParser\JsonDecoder;
use PHPUnit\Util\Json;
use App\Exports\CustomExport;
use App\Exports\TaskExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Report'))
        {
            $reports = Report::where('created_by', \Auth::user()->creatorId())->get();
            return view('report.index', compact('reports'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create Report'))
        {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $entity_type = Report::$entity_type;
            $chart_type = Report::$chart_type;

            return view('report.create', compact('user', 'entity_type', 'chart_type'));
        }
        else
        {
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
        if(\Auth::user()->can('Create Report'))
        {
            $validator = \Validator::make(
                $request->all(), [
                               'name' => 'required|max:120',
                               'entity_type' => 'required',
                               'group_by' => 'required',
                               'chart_type' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $report            = new Report();
        $report['user_id'] = $request->user;
        $report['name']    = $request->name;
        $report['entity_type'] = $request->entity_type;
        $report['group_by']    = $request->group_by;
        $report['chart_type']  = $request->chart_type;
        $report['created_by']  = \Auth::user()->creatorId();
        $report->save();

        return redirect()->back()->with('success', __('Report Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Report $report
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Report $report)
    {
        if(\Auth::user()->can('Show Report'))
        {

            $group_by    = $report->group_by;
            $entity_type = $report->entity_type;
            if($entity_type == 'products')
            {
                $data =Product::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'users')
            {
                $data =User::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'tasks')
            {
                $data =Task::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'sales_orders')
            {
                $data = SalesOrder::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'quotes')
            {
                $data = Quote::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'opportunities')
            {
                $data = Opportunities::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'leads')
            {
                $data = Lead::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'invoices')
            {
                $data = Invoice::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'contacts')
            {
                $data =Contact::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'cases')
            {
                $data =CommonCase::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'campaigns')
            {
                $data =Campaign::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'calls')
            {
                $data =Call::select('*', DB::raw('count(id) as `count`'));
            }
            elseif($entity_type == 'accounts')
            {
                $data =Account::select('*', DB::raw('count(id) as `count`'));

            }

            $data->groupBy($group_by);
            if(!empty($request->start_date && $request->end_date) && $entity_type == 'quote')
            {
                $data->whereBetween(
                    'date_quoted', [
                                     $request->start_date,
                                     $request->end_date,
                                 ]
                );
            }
            else if(!empty($request->start_date && $request->end_date) && $entity_type == 'invoices')
            {
                $data->whereBetween(
                    'date_quoted', [
                                     $request->start_date,
                                     $request->end_date,
                                 ]
                );
            }
            else if(!empty($request->start_date && $request->end_date) && $entity_type == 'tasks')
            {
                $data->whereBetween(
                    'start_date', [
                                    $request->start_date,
                                    $request->end_date,
                                ]
                );
            }
            else if(!empty($request->start_date && $request->end_date) && $entity_type == 'calls')
            {
                $data->whereBetween(
                    'start_date', [
                                    $request->start_date,
                                    $request->end_date,
                                ]
                );
            }
            if((isset($request->start_date) && !empty($request->start_date)) && (isset($request->end_date) && !empty($request->end_date)))
            {
                $data->whereBetween(
                    'created_at', [
                                    $request->start_date,
                                    $request->end_date,
                                ]
                );
            }

            $report['startDateRange'] = $request->start_date;
            $report['endDateRange']   = $request->end_date;

            $data->where('created_by', \Auth::user()->creatorId());
            $data = $data->get()->toArray();

            $label  = [];
            $record = [];
            foreach($data as $user)
            {
                if($entity_type == 'users'){
                    $groupBy  = $group_by . '_name';
                    $label[]  = $user[$groupBy];
                    $record[] = $user['count'];
                }
                else{
                // $groupBy  = $group_by . '_name';
                $user_data = User::getIdByUser($user['user_id']);

                $groupBy  = $group_by;
                $label[]  = $user_data->name ?? '   ';
                $record[] = $user['count'];
                }
            }
            return view('report.view', compact('report', 'data', 'entity_type', 'group_by', 'label', 'record'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Report $report
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        if(\Auth::user()->can('Edit Report'))
        {
            $user        = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $entity_type = Report::$entity_type;
            $group_by = $report->group_by;
            $chart_type = Report::$chart_type;

            return view('report.edit', compact('report', 'entity_type', 'chart_type', 'user', 'group_by'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Report $report
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        if(\Auth::user()->can('Edit Report'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'name' => 'required|max:120',
                                'chart_type' => 'required',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $report['user_id'] = $request->user;
            $report['name']    = $request->name;
            $report['chart_type'] = $request->chart_type;
            $report['created_by'] = \Auth::user()->creatorId();
            $report->save();

            return redirect()->route('report.index')->with('success', __('Report Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Report $report
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        if(\Auth::user()->can('Delete Report'))
        {
            $report->delete();

            return redirect()->back()->with('success', __('Report Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function leadsanalytic(Request $request)
    {
        $report['source'] = __('All');
        $report['status'] = __('All');
        $leadsource       = LeadSource::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $leadsource->prepend('Select Source', 0);
        $status = Lead::$status;


        $labels = [];
        $data   = [];


        if(!empty($request->start_month) && !empty($request->end_month))
        {
            $start = strtotime($request->start_month);
            $end   = strtotime($request->end_month);
        }
        else
        {
            $start = strtotime(date('Y-01'));
            $end   = strtotime(date('Y-12'));
        }

        $leads = Lead::orderBy('id');

        $leads->where('created_at', '>=', date('Y-m-01', $start))->where('created_at', '<=', date('Y-m-t', $end));
        if(!empty($request->leadsource))
        {
            $leads->where('source', $request->leadsource);
        }
        if(!empty($request->status))
        {
            $leads->where('status', $request->status);
        }

        $leads->where('created_by', \Auth::user()->creatorId());
        $leads = $leads->get();

        $currentdate = $start;
        while($currentdate <= $end)
        {
            $month = date('m', $currentdate);
            $year  = date('Y', $currentdate);

            $leadFilter = Lead::where('created_by', \Auth::user()->creatorId())->whereMonth('created_at', $month)->whereYear('created_at', $year);
            if(!empty($request->leadsource))
            {
                $leadFilter->where('source', $request->leadsource);
            }
            if(!empty($request->status))
            {
                $leadFilter->where('status', $request->status);
            }

            $leadFilter->where('created_by', \Auth::user()->creatorId());
            $leadFilter = $leadFilter->get();

            $data[]      = count($leadFilter);
            $labels[]    = date('M Y', $currentdate);
            $currentdate = strtotime('+1 month', $currentdate);

        }

        $report['startDateRange'] = date('M-Y', $start);
        $report['endDateRange']   = date('M-Y', $end);

        return view('report.leadsanalytic', compact('labels', 'data', 'report', 'leads', 'leadsource', 'status'));

    }

    public function invoiceanalytic(Request $request)
    {
        $report['account'] = __('All');
        $report['status']  = __('All');
        $account           = Account::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $account->prepend('Select Account', 0);
        $status = Invoice::$status;

        $data = [];

        if(!empty($request->start_month) && !empty($request->end_month))
        {
            $start = strtotime($request->start_month);
            $end   = strtotime($request->end_month);
        }
        else
        {
            $start = strtotime(date('Y-01'));
            $end   = strtotime(date('Y-12'));
        }
        $invoice = Invoice::selectRaw('invoices.*,MONTH(date_quoted) as month,YEAR(date_quoted) as year')->orderBy('id');

        $invoice->where('date_quoted', '>=', date('Y-m-01', $start))->where('date_quoted', '<=', date('Y-m-t', $end));
        if(!empty($request->account))
        {
            $invoice->where('account', $request->account);
        }
        if(!empty($request->status))
        {
            $invoice->where('status', $request->status);
        }

        $invoice->where('created_by', \Auth::user()->creatorId());
        $invoice = $invoice->get();

        $totalInvoice      = 0;
        $totalDueInvoice   = 0;
        $invoiceTotalArray = [];
        foreach($invoice as $invoic)
        {
            $totalInvoice                        += $invoic->getTotal();
            $invoiceTotalArray[$invoic->month][] = $invoic->getTotal();
        }

        for($i = 1; $i <= 12; $i++)
        {
            $invoiceTotal[] = array_key_exists($i, $invoiceTotalArray) ? array_sum($invoiceTotalArray[$i]) : 0;
        }
        $monthList = $month = $this->yearMonth();

        $report['startDateRange'] = date('M-Y', $start);
        $report['endDateRange']   = date('M-Y', $end);

        return view('report.invoiceanalytic', compact('monthList', 'data', 'report', 'invoice', 'account', 'status', 'totalInvoice', 'invoiceTotal'));
    }

    public function yearMonth()
    {

        $month[] = __('January');
        $month[] = __('February');
        $month[] = __('March');
        $month[] = __('April');
        $month[] = __('May');
        $month[] = __('June');
        $month[] = __('July');
        $month[] = __('August');
        $month[] = __('September');
        $month[] = __('October');
        $month[] = __('November');
        $month[] = __('December');

        return $month;
    }

    public function salesorderanalytic(Request $request)
    {
        $status = SalesOrder::$status;

        $data = [];

        if(!empty($request->start_month) && !empty($request->end_month))
        {
            $start = strtotime($request->start_month);
            $end   = strtotime($request->end_month);
        }
        else
        {
            $start = strtotime(date('Y-01'));
            $end   = strtotime(date('Y-12'));
        }
        $salesorder = SalesOrder::selectRaw('sales_orders.*,MONTH(date_quoted) as month,YEAR(date_quoted) as year')->orderBy('id');

        $salesorder->where('date_quoted', '>=', date('Y-m-01', $start))->where('date_quoted', '<=', date('Y-m-t', $end));

        if(!empty($request->status))
        {
            $salesorder->where('status', $request->status);
        }

        $salesorder->where('created_by', \Auth::user()->creatorId())->with('accounts','quotes');
        $salesorder = $salesorder->get();

        $totalSalesorder      = 0;
        $salesorderTotalArray = [];
        foreach($salesorder as $salesord)
        {
            $totalSalesorder                          += $salesord->getTotal();
            $salesorderTotalArray[$salesord->month][] = $salesord->getTotal();
        }

        for($i = 1; $i <= 12; $i++)
        {
            $salesorderTotal[] = array_key_exists($i, $salesorderTotalArray) ? array_sum($salesorderTotalArray[$i]) : 0;
        }
        $monthList = $month = $this->yearMonth();

        $report['startDateRange'] = date('M-Y', $start);
        $report['endDateRange']   = date('M-Y', $end);

        return view('report.salesorderanalytic', compact('monthList', 'data', 'report', 'salesorder', 'status', 'totalSalesorder', 'salesorderTotal'));

    }

    public function quoteanalytic(Request $request)
    {
        $status = Quote::$status;

        $data = [];

        if(!empty($request->start_month) && !empty($request->end_month))
        {
            $start = strtotime($request->start_month);
            $end   = strtotime($request->end_month);
        }
        else
        {
            $start = strtotime(date('Y-01'));
            $end   = strtotime(date('Y-12'));
        }
        $quote = Quote::selectRaw('quotes.*,MONTH(date_quoted) as month,YEAR(date_quoted) as year')->orderBy('id');

        $quote->where('date_quoted', '>=', date('Y-m-01', $start))->where('date_quoted', '<=', date('Y-m-t', $end));
        if(!empty($request->status))
        {
            $quote->where('status', $request->status);
        }

        $quote->where('created_by', \Auth::user()->creatorId())->with('assign_user','accounts');
        $quote = $quote->get();
        $totalQuote = 0;
        $quoteTotalArray = [];
        foreach($quote as $quot)
        {
            $totalQuote                     += $quot->getTotal();
            $quoteTotalArray[$quot->month][] = $quot->getTotal();
        }

        for($i = 1; $i <= 12; $i++)
        {
            $quoteTotal[] = array_key_exists($i, $quoteTotalArray) ? array_sum($quoteTotalArray[$i]) : 0;
        }
        $monthList = $month = $this->yearMonth();

        $report['startDateRange'] = date('M-Y', $start);
        $report['endDateRange']   = date('M-Y', $end);

        return view('report.quoteanalytic', compact('monthList', 'data', 'report','quote', 'totalQuote', 'status', 'quoteTotal'));
    }

    public function supportanalytic()
    {
        return view('report.supportanalytic');
    }

    public function getparent(Request $request)
    {

        if($request->parent == 'accounts')
        {
            $parent = Report::$accounts;
        }
        elseif($request->parent == 'leads')
        {
            $parent = Report::$leads;
        }
        elseif($request->parent == 'contacts')
        {
            $parent = Report::$contacts;
        }
        elseif($request->parent == 'opportunities')
        {
            $parent = Report::$opportunities;
        }
        elseif($request->parent == 'cases')
        {
            $parent = Report::$cases;
        }
        elseif($request->parent == 'users')
        {
            $parent = Report::$users;
        }
        elseif($request->parent == 'tasks')
        {
            $parent = Report::$tasks;
        }
        elseif($request->parent == 'sales_orders')
        {
            $parent = Report::$sales_orders;
        }
        elseif($request->parent == 'quotes')
        {
            $parent = Report::$quotes;
        }
        elseif($request->parent == 'products')
        {
            $parent = Report::$products;
        }
        elseif($request->parent == 'invoices')
        {
            $parent = Report::$invoices;
        }
        elseif($request->parent == 'campaigns')
        {
            $parent = Report::$campaigns;
        }
        elseif($request->parent == 'calls')
        {
            $parent = Report::$calls;
        }
        else
        {
            $parent = '';
        }

        return response()->json($parent);
    }

    // public function fileExport()
    // {
    //     $name = 'quote_' . date('Y-m-d i:h:s');
    //     $data = Excel::download(new CustomExport(), $name . '.xlsx'); ob_end_clean();
    //     return $data;
    // }
    // public function customreport()
    // {
    //     return view('report.index');
    // }
        public function  fileexport()
    {
        $name = 'export' . date('Y-m-d i:h:s');
        $data = Excel::download(new CustomExport(), $name . '.xlsx');  ob_end_clean();

        return $data;
    }
}
