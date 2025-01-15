<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\LeadSource;
use App\Models\Opportunities;
use App\Models\OpportunitiesStage;
use App\Models\Plan;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Models\Stream;
use App\Models\Task;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;
use App\Models\User;

class OpportunitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Opportunities'))
        {
            if(\Auth::user()->type == 'owner'){
            $opportunitiess = Opportunities::with('accounts','stages','assign_user')->where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'opportunities';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);
        }
        else{
            $opportunitiess = Opportunities::with('accounts','stages','assign_user')->where('user_id', \Auth::user()->id)->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'opportunities';
            $defualtView->view   = 'list';
        }
            return view('opportunities.index', compact('opportunitiess'));
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
    public function create($type, $id)
    {
        if(\Auth::user()->can('Create Opportunities'))
        {
            $account_name        = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account_name->prepend('--', '');
            $user                = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $opportunities_stage = OpportunitiesStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $leadsource          = LeadSource::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $contact             = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $contact->prepend('--', '');
            $campaign_id         = Campaign::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $campaign_id->prepend('--', '');

            return view('opportunities.create', compact('user', 'opportunities_stage', 'leadsource', 'account_name', 'contact', 'type', 'id', 'campaign_id'));
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
        if(\Auth::user()->can('Create Opportunities'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'amount' => 'required|numeric',
                                   'probability' => 'required|numeric',
                                   'stage' => 'required',
                                   'lead_source' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $opportunities                = new Opportunities();
            $opportunities['user_id']     = $request->user;
            $opportunities['campaign']    = !empty($request->campaign) ? $request->campaign : '';
            $opportunities['name']        = $request->name;
            $opportunities['account']     = $request->account;
            $opportunities['stage']       = $request->stage;
            $opportunities['amount']      = $request->amount;
            $opportunities['probability'] = $request->probability;
            $opportunities['close_date']  = $request->close_date;
            $opportunities['contact']     = $request->contact;
            $opportunities['lead_source'] = $request->lead_source;
            $opportunities['description'] = $request->description;
            $opportunities['created_by']  = \Auth::user()->creatorId();
            $opportunities->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunities->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Opportunities Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Opportunities $opportunities
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(\Auth::user()->can('Show Opportunities'))
        {
            $opportunities = Opportunities::find($id);
            $lead_source   = LeadSource::find($id);
            $satge         = OpportunitiesStage::find($id);
            $account_name  = Account::find($id);


            return view('opportunities.view', compact('opportunities', 'lead_source', 'satge', 'account_name'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Opportunities $opportunities
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->can('Edit Opportunities'))
        {
            $opportunities = Opportunities::find($id);
            $stages        = OpportunitiesStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $lead_source   = LeadSource::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user          = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $account_name  = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account_name->prepend('--', '');
            $campaign_id   = Campaign::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $campaign_id->prepend('--', '');
            $contact       = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $contact->prepend('--', '');
            $documents     = Document::where('opportunities', $opportunities->id)->get();
            $parent        = 'opportunities';
            $tasks         = Task::where('parent', $parent)->where('parent_id', $opportunities->id)->get();
            $log_type      = 'opportunities comment';
            $streams       = Stream::where('log_type', $log_type)->get();
            $quotes        = Quote::where('opportunity', $opportunities->id)->get();
            $salesorders   = SalesOrder::where('opportunity', $opportunities->id)->get();
            $salesinvoices = Invoice::where('opportunity', $opportunities->id)->get();


            // get previous user id
            $previous = Opportunities::where('id', '<', $opportunities->id)->max('id');
            // get next user id
            $next = Opportunities::where('id', '>', $opportunities->id)->min('id');


            return view('opportunities.edit', compact('opportunities','salesinvoices','salesorders','quotes', 'user', 'lead_source', 'stages', 'account_name', 'contact', 'documents', 'tasks', 'streams', 'campaign_id', 'previous', 'next'));
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
     * @param \App\Opportunities $opportunities
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('Edit Opportunities'))
        {
            $opportunities = Opportunities::find($id);
            $validator     = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'amount' => 'required|numeric',
                                   'probability' => 'required|numeric',

                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $opportunities['user_id']     = $request->user;
            $opportunities['campaign'] = $request->campaign;
            $opportunities['name']        = $request->name;
            $opportunities['account']     = $request->account;
            $opportunities['contact']    = $request->contact;
            $opportunities['stage']       = $request->stage;
            $opportunities['amount']      = $request->amount;
            $opportunities['probability'] = $request->probability;
            $opportunities['close_date']  = $request->close_date;
            $opportunities['lead_source'] = $request->lead_source;
            $opportunities['description'] = $request->description;
            $opportunities['created_by']  = \Auth::user()->creatorId();
            $opportunities->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunities->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Opportunities Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Opportunities $opportunities
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->can('Delete Opportunities'))
        {
            $opportunities = Opportunities::find($id);

            $opportunities->delete();

            return redirect()->back()->with('success', 'Opportunities ' . $opportunities->name . ' Deleted!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {

        $stages         = OpportunitiesStage::where('created_by', '=', \Auth::user()->creatorId())->get();
        $opportunitiess = Opportunities::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'opportunities';
        $defualtView->view   = 'kanban';
        User::userDefualtView($defualtView);

        return view('opportunities.grid', compact('opportunitiess', 'stages'));
    }

    public function changeorder(Request $request)
    {
        $post          = $request->all();
        $opportunities = Opportunities::find($post['opo_id']);
        $stage         = OpportunitiesStage::find($post['stage_id']);


        if(!empty($stage))
        {
            $opportunities->stage = $post['stage_id'];
            $opportunities->save();
        }

        foreach($post['order'] as $key => $item)
        {
            $order        = Opportunities::find($item);
            $order->stage = $post['stage_id'];
            $order->save();
        }
    }
}
