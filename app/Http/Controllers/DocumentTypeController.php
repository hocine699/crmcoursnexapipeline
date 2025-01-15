<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage DocumentType'))
        {
            if(\Auth::user()->type == 'owner'){
            $types = DocumentType::where('created_by',\Auth::user()->creatorId())->get();
        }
        else{
            $types = DocumentType::where('created_by',\Auth::user()->id)->get();

        }
            return view('document_type.index', compact('types'));
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
        if(\Auth::user()->can('Create DocumentType'))
        {
            return view('document_type.create');
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
        if(\Auth::user()->can('Create DocumentType'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );

            $name                  = $request['name'];
            $documenttype          = new DocumentType();
            $documenttype->name    = $name;
            $documenttype['created_by'] = \Auth::user()->creatorId();
            $documenttype->save();

            return redirect()->route('document_type.index')->with('success', 'Document Type'.' '. $documenttype->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DocumentType $documentType
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentType $documentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\DocumentType $documentType
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentType $documentType)
    {
        if(\Auth::user()->can('Edit DocumentType'))
        {
            return view('document_type.edit', compact('documentType'));
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
     * @param \App\DocumentType $documentType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentType $documentType)
    {
        if(\Auth::user()->can('Edit DocumentType'))
        {
            $this->validate(
                $request, ['name' => 'required|max:40',]
            );
            $documentType['name']       = $request->name;
            $documentType['created_by'] = \Auth::user()->creatorId();
            $documentType->update();

            return redirect()->route('document_type.index')->with(
                'success', 'Document Type ' . $documentType->name . ' updated!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DocumentType $documentType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentType $documentType)
    {
        if(\Auth::user()->can('Delete DocumentType'))
        {
            $documentType->delete();

            return redirect()->route('document_type.index')->with(
                'success', 'Document Type ' . $documentType->name . ' Deleted!'
            );
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
