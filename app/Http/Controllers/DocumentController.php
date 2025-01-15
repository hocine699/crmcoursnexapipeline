<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\DocumentType;
use App\Models\Opportunities;
use App\Models\Plan;
use App\Models\Stream;
use App\Models\Utility;
use App\Models\User;
use App\Models\UserDefualtView;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Document'))
        {
            if(\Auth::user()->type == 'owner'){
                $documents = Document::with('assign_user')->where('created_by', \Auth::user()->creatorId())->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'document';
                $defualtView->view   = 'list';
                User::userDefualtView($defualtView);
            }
            else{
                $documents = Document::with('assign_user')->where('user_id', \Auth::user()->id)->get();

                $defualtView         = new UserDefualtView();
                $defualtView->route  = \Request::route()->getName();
                $defualtView->module = 'document';
                $defualtView->view   = 'list';
            }
            return view('document.index', compact('documents'));
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
        if(\Auth::user()->can('Create Document'))
        {
            $status  = Document::$status;
            $user    = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $folder  = DocumentFolder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', 0);
            $types   = DocumentType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $types->prepend('none', '');
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $opportunities->prepend('--', 0);

            return view('document.create', compact('status', 'user', 'folder', 'types', 'type', 'id', 'account', 'opportunities'));
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
        if(\Auth::user()->can('Create Document'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'folder' => 'required',
                                   'type' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $document                    = new Document();
            $document['user_id']         = $request->user;
            $document['name']            = $request->name;
            $document['account']         = $request->account;
            $document['folder']          = $request->folder;
            $document['opportunities']   = $request->opportunities;
            $document['type']            = $request->type;
            $document['status']          = $request->status;
            $document['publish_date']    = $request->publish_date;
            $document['expiration_date'] = $request->expiration_date;
            $document['description']     = $request->description;
            if(!empty($request->attachment))
            {
                $image_size = $request->file('attachment')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1)
                {
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir        = 'upload/profile/';

                if(\File::exists($dir))
                {
                    \File::delete($dir);
                }
                $url = '';
            $document['attachment']      = !empty($request->attachment) ? $fileNameToStore : '';
                $path = Utility::upload_file($request,'attachment',$fileNameToStore,$dir,[]);

                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            }


            $document['created_by']      = \Auth::user()->creatorId();
            $document->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'document',
                            'stream_comment' => '',
                            'user_name' => $document->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Document Successfully Created.'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        if(\Auth::user()->can('Show Document'))
        {

            return view('document.view', compact('document'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Document $document)
    {
        if(\Auth::user()->can('Edit Document'))
        {
            $folders = DocumentFolder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $type    = DocumentType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $type->prepend('none', '');
            $status        = Document::$status;
            $user          = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $user->prepend('--', 0);
            $accounts      = Account::where('document_id', $document->id)->get();
            // $accounts->prepend('--', 0);
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $opportunities->prepend('--', 0);
            $account_name = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $account_name->prepend('--', 0);

            // get previous user id
            $previous = Document::where('id', '<', $document->id)->max('id');
            // get next user id
            $next = Document::where('id', '>', $document->id)->min('id');

            return view('document.edit', compact('document', 'folders', 'type', 'status', 'user', 'accounts', 'opportunities','previous','next','account_name'));
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
     * @param \App\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        if(\Auth::user()->can('Edit Document'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',

                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(!empty($request->attachment))
            {
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $dir        = 'upload/profile/';

                if(\File::exists($dir))
                {
                    \File::delete($dir);
                }
                $url = '';
                $path = Utility::upload_file($request,'attachment',$fileNameToStore,$dir,[]);
                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            $document['user_id']         = $request->user;
            $document['name']            = $request->name;
            $document['account']         = $request->account;
            $document['folder']          = $request->folder;
            $document['type']            = $request->type;
            $document['opportunities']   = $request->opportunities;
            $document['status']          = $request->status;
            $document['publish_date']    = $request->publish_date;
            $document['expiration_date'] = $request->expiration_date;
            $document['description']     = $request->description;
            if(!empty($request->attachment))
            {
                $document['attachment'] = $fileNameToStore;
            }
            $document['created_by'] = \Auth::user()->creatorId();
            $document->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'document',
                            'stream_comment' => '',
                            'user_name' => $document->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Document Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        if(\Auth::user()->can('Delete Document'))
        {
            $file_path = 'upload/profile/'.$document->attachment;
            $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
            // Utility::changeStorageLimit(\Auth::user()->creatorId(), $image_path);
            $document->delete();

            return redirect()->back()->with('success', __('Document Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        if(\Auth::user()->type == 'owner'){
            $documents = Document::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'document';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);
        }
        else{
            $documents = Document::where('user_id', \Auth::user()->id)->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'document';
            $defualtView->view   = 'list';
        }
        return view('document.grid', compact('documents'));


    }
}
