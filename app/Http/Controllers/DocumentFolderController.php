<?php

namespace App\Http\Controllers;

use App\Models\DocumentFolder;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Parent_;

class DocumentFolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\Auth::user()->can('Manage DocumentFolder')) {
            if (\Auth::user()->type == 'owner') {
                // $folders = DocumentFolder::where('created_by', \Auth::user()->creatorId())->get();
                $folders = DocumentFolder::with('children')->where('created_by', \Auth::user()->creatorId())->get();
            } else {
                $folders = DocumentFolder::with('children')->where('created_by', \Auth::user()->id)->get();
            }
            return view('document_folder.index', compact('folders'));
        } else {
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
        if (\Auth::user()->can('Create DocumentFolder')) {
            $parent = DocumentFolder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $parent->prepend('select parent', 0);

            return view('document_folder.create', compact('parent'));
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
        if (\Auth::user()->can('Create DocumentFolder')) {
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

            $documentfolder               = new DocumentFolder();
            $documentfolder->name         = $request['name'];
            $documentfolder->parent       = $request['parent'];
            $documentfolder->description  = $request['description'];
            $documentfolder['created_by'] = \Auth::user()->creatorId();
            $documentfolder->save();

            return redirect()->route('document_folder.index')->with('success', 'Document Folders' . $documentfolder->name . ' added!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DocumentFolder $documentFolder
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentFolder $documentFolder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\DocumentFolder $documentFolder
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentFolder $documentFolder)
    {
        if (\Auth::user()->can('Edit DocumentFolder')) {
            $parent = DocumentFolder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $parent->prepend('select parent', 0);

            return view('document_folder.edit', compact('documentFolder', 'parent'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\DocumentFolder $documentFolder
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentFolder $documentFolder)
    {
        if (\Auth::user()->can('Edit DocumentFolder')) {
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

            $documentFolder->name         = $request['name'];
            $documentFolder->parent       = $request['parent'];
            $documentFolder->description  = $request['description'];
            $documentFolder['created_by'] = \Auth::user()->creatorId();
            $documentFolder->save();

            return redirect()->route('document_folder.index')->with('success', 'Document Folders' . $documentFolder->name . ' Updated!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DocumentFolder $documentFolder
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentFolder $documentFolder)
    {
        if (\Auth::user()->can('Delete DocumentFolder')) {
            $documentFolder->delete();

            return redirect()->route('document_folder.index')->with('success', 'Document Folders' . $documentFolder->name . ' Deleted!');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
