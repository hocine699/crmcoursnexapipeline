<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contact;
use App\Models\FormBuilder;
use App\Models\FormField;
use App\Models\FormFieldResponse;
use App\Models\FormResponse;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeadStage;
use App\Models\UserLead;



class FormBuilderController extends Controller
{

    public function index()
    {
        $usr = \Auth::user();
        if ($usr->can('Manage Form Builder')) {
            if (\Auth::user()->type == 'owner') {

                $forms = FormBuilder::where('created_by', '=', $usr->creatorId())->get();
            } else {
                $forms = FormBuilder::where('created_by', '=', $usr->id)->get();
            }
            return view('form_builder.index', compact('forms'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        return view('form_builder.create');
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Form Builder')) {
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('form_builder.index')->with('error', $messages->first());
            }

            $form_builder             = new FormBuilder();
            $form_builder->name       = $request->name;
            $form_builder->code       = uniqid() . time();
            $form_builder->is_active  = $request->is_active;
            $form_builder->created_by = \Auth::user()->creatorId();
            $form_builder->save();

            return redirect()->route('form_builder.index')->with('success', __('Form successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show(FormBuilder $formBuilder)
    {

        if (\Auth::user()->can('Show Form Builder')) {
            return view('form_builder.show', compact('formBuilder'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }


    public function edit(FormBuilder $formBuilder)
    {
        return view('form_builder.edit', compact('formBuilder'));
    }


    public function update(Request $request, FormBuilder $formBuilder)
    {
        $usr = \Auth::user();
        if ($usr->can('Edit Form Builder')) {
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('form_builder.index')->with('error', $messages->first());
            }

            $formBuilder->name      = $request->name;
            $formBuilder->is_active = $request->is_active;
            $formBuilder->save();

            return redirect()->route('form_builder.index')->with('success', __('Form successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(FormBuilder $formBuilder)
    {
        if (\Auth::user()->can('Delete Form Builder')) {
            FormField::where('form_id', '=', $formBuilder->id)->delete();
            FormFieldResponse::where('form_id', '=', $formBuilder->id)->delete();
            FormResponse::where('form_id', '=', $formBuilder->id)->delete();

            $formBuilder->delete();

            return redirect()->route('form_builder.index')->with('success', __('Form successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    // Field curd
    public function fieldCreate($id)
    {
        $formbuilder = FormBuilder::find($id);
        $types       = FormBuilder::$fieldTypes;

        return view('form_builder.field_create', compact('types', 'formbuilder'));
    }

    public function fieldStore($id, Request $request)
    {
        $usr = \Auth::user();
        if ($usr->can('Create Form Field')) {
            $formbuilder = FormBuilder::find($id);
            if ($formbuilder->created_by == $usr->creatorId()) {
                $names = $request->name;
                $types = $request->type;

                foreach ($names as $key => $value) {
                    if (!empty($value)) {
                        // create form field
                        FormField::create([
                            'form_id' => $formbuilder->id,
                            'name' => $value,
                            'type' => $types[$key],
                            'created_by' => $usr->creatorId(),
                        ]);
                    }
                }

                return redirect()->back()->with('success', __('Field successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldEdit($id, $field_id)
    {
        $form       = FormBuilder::find($id);
        $form_field = FormField::find($field_id);

        $types = FormBuilder::$fieldTypes;

        return view('form_builder.field_edit', compact('form_field', 'types', 'form'));
    }

    public function fieldUpdate($id, $field_id, Request $request)
    {
        $usr = \Auth::user();
        if ($usr->can('Edit Form Field')) {
            $form = FormBuilder::find($id);
            if ($form->created_by == $usr->creatorId()) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $field = FormField::find($field_id);
                $field->update([
                    'name' => $request->name,
                    'type' => $request->type,
                ]);

                return redirect()->back()->with('success', __('Form successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldDestroy($id, $field_id)
    {
        $usr = \Auth::user();
        if ($usr->can('Delete Form Field')) {
            $form = FormBuilder::find($id);
            if ($form->created_by == $usr->creatorId()) {
                $form_field_response = FormFieldResponse::orWhere('name_id', '=', $field_id)->orWhere('email_id', '=', $field_id)->orWhere('phone_id', '=', $field_id)->orWhere('state_id', '=', $field_id)->orWhere('address_id', '=', $field_id)->orWhere('city_id', '=', $field_id)->orWhere('country_id', '=', $field_id)->orWhere('postal_code', '=', $field_id)->first();

                if (!empty($form_field_response)) {
                    return redirect()->back()->with('error', __('Please remove this field from Convert Lead.'));
                } else {
                    $form_field = FormField::find($field_id);
                    if (!empty($form_field)) {
                        $form_field->delete();
                    } else {
                        return redirect()->back()->with('error', __('Field not found.'));
                    }

                    return redirect()->back()->with('success', __('Form successfully deleted.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // For Response
    public function viewResponse($form_id)
    {
        if (Auth::user()->type == 'owner') {
            $form = FormBuilder::find($form_id);
            if ($form->created_by == \Auth::user()->creatorId()) {
                return view('form_builder.response', compact('form'));
            } else {
                return response()->json(['error' => __('Permission Denied . ')], 401);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // For Response Detail
    public function responseDetail($response_id)
    {
        if (\Auth::user()->type == 'owner') {
            $formResponse = FormResponse::find($response_id);
            $form         = FormBuilder::find($formResponse->form_id);
            if ($form->created_by == \Auth::user()->creatorId()) {
                $response = json_decode($formResponse->response, true);

                return view('form_builder.response_detail', compact('response'));
            } else {
                return response()->json(['error' => __('Permission Denied . ')], 401);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // For Front Side View
    public function formView($code)
    {
        if (!empty($code)) {
            $form = FormBuilder::where('code', 'LIKE', $code)->first();
            if (!empty($form)) {
                if ($form->is_active == 1) {
                    $objFields = $form->form_field;

                    return view('form_builder.form_view', compact('objFields', 'code', 'form'));
                } else {
                    return view('form_builder.form_view', compact('code', 'form'));
                }
            } else {
                return redirect()->route('login')->with('error', __('Form not found please contact to admin.'));
            }
        } else {
            return redirect()->route('login')->with('error', __('Permission Denied.'));
        }
    }

    // For Front Side View Store
    public function formViewStore(Request $request)
    {
        // Get form
        $form = FormBuilder::where('code', 'LIKE', $request->code)->first();
        if (!empty($form)) {
            $arrFieldResp = [];
            if ($request->field != null) {
                foreach ($request->field as $key => $value) {
                    $arrFieldResp[FormField::find($key)->name] = (!empty($value)) ? $value : '-';
                }
            }
            // store response
            $dd = FormResponse::create([
                'form_id' => $form->id,
                'response' => json_encode($arrFieldResp),
            ]);

            // in form convert lead is active then creat lead
            // if ($form->is_lead_active == 1) {

            //     $objField = $form->fieldResponse;

            //     // validation
            //     $email = User::where('email', 'LIKE', $request->field[$objField->email_id])->first();

            //     if (!empty($email)) {
            //         return redirect()->back()->with('error', __('Email already exist in our record.!'));
            //     }

            //     $usr = User::find($form->created_by);
            //     // $stage = LeadStage::where('pipeline_id', '=', $objField->pipeline_id)->first();


            //     $lead                  = new Lead();
            //     $lead->name            = $request->field[$objField->name_id];
            //     $lead->email           = $request->field[$objField->email_id];
            //     $lead->phone           = $request->field[$objField->phone_id];
            //     $lead->lead_address    = $request->field[$objField->address_id];
            //     $lead->lead_city       = $request->field[$objField->city_id];
            //     $lead->lead_state      = $request->field[$objField->state_id];
            //     $lead->lead_country    = $request->field[$objField->country_id];
            //     $lead->lead_postalcode = $request->field[$objField->postal_code];
            //     $lead->description     = $request->field[$objField->description_id];
            //     $lead->user_id         = $objField->user_id;
            //     $lead->created_by      = $usr->creatorId();
            //     $lead->save();
            // }
            // return response()->json(['success' => __('Data submit successfully ')]);

            return redirect()->back()->with('success', __('Data submit successfully.'));
        } else {
            return redirect()->route('login')->with('error', __('Something went wrong.'));
        }
    }

    // Convert into lead Modal
    public function formFieldBind($form_id)
    {
        $usr = \Auth::user();

        $form = FormBuilder::find($form_id);

        if ($form->created_by == $usr->creatorId()) {
            $types = $form->form_field->pluck('name', 'id');

            $formField = FormFieldResponse::where('form_id', '=', $form_id)->first();

            // Get Users
            $users = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $users->prepend('--', 0);


            return view('form_builder.form_field', compact('form', 'types', 'formField', 'users'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Store convert into lead modal
    public function bindStore(Request $request, $id)
    {

        $usr = Auth::user();
        if ($usr->type == 'owner') {
            $form                    = FormBuilder::find($id);
            $form->is_lead_active    = $request->is_lead_active;
            $form->is_account_active = $request->is_account_active;
            $form->is_contact_active = $request->is_contact_active;
            $form->save();

            if ($request->is_lead_active == 1 || $request->is_account_active == 1 || $request->is_contact_active == 1) {
                $validator = \Validator::make($request->all(), [
                    'name_id' => 'required',
                    'email_id' => 'required',
                    'phone_id' => 'required',
                    'address_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'country_id' => 'required',
                    'postal_code' => 'required',
                    'user_id' => 'required',
                ]);


                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    // if validation failed then make status 0
                    $form->is_lead_active = 0;
                    $form->save();

                    return redirect()->back()->with('error', $messages->first());
                }

                if (!empty($request->form_response_id)) {
                    // if record already exists then update it.
                    $field_bind = FormFieldResponse::find($request->form_response_id);

                    $field_bind->update([
                        'name_id' => $request->name_id,
                        'email_id' => $request->email_id,
                        'phone_id' => $request->phone_id,
                        'address_id' => $request->address_id,
                        'city_id' => $request->city_id,
                        'state_id' => $request->state_id,
                        'country_id' => $request->country_id,
                        'postal_code' => $request->postal_code,
                        'user_id' => $request->user_id,
                        'type' => $request->is_convert,
                        'description_id' => $request->description_id,
                    ]);
                } else {

                    // Create Field Binding record on form_field_responses tbl
                    FormFieldResponse::create([
                        'form_id' => $request->form_id,
                        'name_id' => $request->name_id,
                        'email_id' => $request->email_id,
                        'phone_id' => $request->phone_id,
                        'address_id' => $request->address_id,
                        'city_id' => $request->city_id,
                        'state_id' => $request->state_id,
                        'country_id' => $request->country_id,
                        'postal_code' => $request->postal_code,
                        'description_id' => $request->description_id,
                        'user_id' => $request->user_id,
                        'type' => $request->is_convert,
                    ]);
                }
            }

            return redirect()->back()->with('success', __('Setting saved successfully!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
