<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Webhook;

class WebhookController extends Controller
{
    // public function index()
    // {
    //     $webhooks = Webhook::where('created_by', Auth::user()->id)->get();
    //     return view('settings.index', compact('webhooks'));
    // }
    public function create()
    {
        $module = [
            'New User' => 'New User ', 'New Lead ' => 'New Lead ', 'New Quotes' => 'New Quotes', 'New Sales Order' => 'New Sales Order',
            'New Invoice' => 'New Invoice', 'New Invoice Payment' => 'New Invoice Payment', 'New Meeting' => 'New Meeting',
            'New Task' => 'New Task'
        ];
        $method = ['GET' => 'GET', 'POST' => 'POST'];
        return view('webhook.create', compact('module', 'method'));
    }
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'module' => 'required',
                'url' => 'required|url',
                'method' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $webhook = new Webhook();
        $webhook->module = $request->module;
        $webhook->url = $request->url;
        $webhook->method = $request->method;
        $webhook->created_by = \Auth::user()->id;
        $webhook->save();
        return redirect()->back()->with('success', __('Webhook Successfully Created.'));
    }
    public function edit(Request $request, $id)
    {
        $module = [
            'New User' => 'New User ', 'New Lead ' => 'New Lead ', 'New Quotes' => 'New Quotes', 'New Sales Order' => 'New Sales Order',
            'New Invoice' => 'New Invoice', 'New Invoice Payment' => 'New Invoice Payment', 'New Meeting' => 'New Meeting',
            'New Task' => 'New Task'
        ];
        $method = ['GET' => 'GET', 'POST' => 'POST'];
        $webhook = Webhook::find($id);

        return view('webhook.edit', compact('webhook', 'module', 'method'));
    }
    public function update(Request $request, $id)
    {
                $webhook['module']       = $request->module;
                $webhook['url']       = $request->url;
                $webhook['method']      = $request->method;
                $webhook['created_by'] = \Auth::user()->creatorId();
                Webhook::where('id', $id)->update($webhook);
                return redirect()->back()->with('success', __('Webhook Setting Succssfully Updated'));
    }
    public function destroy($id)
    {
        $webhook = Webhook::find($id);
        $webhook->delete();

        return redirect()->back()->with('success', __('Webhook successfully deleted.'));
    }

}
