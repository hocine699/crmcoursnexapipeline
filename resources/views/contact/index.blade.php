@extends('layouts.admin')
@section('page-title')
    {{ __('Contact') }}
@endsection
@section('title')
    {{ __('Contact') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Contact') }}</li>
@endsection
@section('action-btn')
    <a href="{{ route('contact.grid') }}" class="btn btn-sm btn-primary btn-icon me-1"
        data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
        <i class="ti ti-layout-grid text-white"></i>
    </a>

    @can('Create Contact')
        <a href="#" data-url="{{ route('contact.create', ['contact', 0]) }}" data-size="lg" data-ajax-popup="true"
            data-bs-toggle="tooltip"data-title="{{ __('Create New Contact') }}"title="{{ __('Create') }}"
            class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="datatable" class="table datatable align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{ __('Email') }}</th>
                                    <th scope="col" class="sort" data-sort="status">{{ __('Phone') }}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{ __('City') }}</th>
                                    <th scope="col" class="sort" data-sort="Assigned User">{{ __('Assigned User') }}
                                    </th>
                                    @if (Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                                        <th scope="col" class="text-end">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td>
                                            <a href="{{ route('contact.edit', $contact->id) }}" data-size="md"
                                                data-title="{{ __('Contact Details') }}" class="action-item text-primary">
                                                {{ ucfirst($contact->name) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="budget">
                                                {{ $contact->email }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="budget">
                                                {{ $contact->phone }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="budget ">{{ ucfirst($contact->contact_city) }}</span>
                                        </td>
                                        <td>
                                            <span class="col-sm-12"><span
                                                    class="text-sm">{{ ucfirst(!empty($contact->assign_user) ? $contact->assign_user->name : '') }}</span></span>
                                        </td>
                                        @if (Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                                            <td class="text-end">
                                                @can('Create Contact')
                                                    <div class="action-btn  me-2">
                                                        <a href="#" data-size="md"
                                                            data-url="{{ route('contact.show', $contact->id) }}"
                                                            data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                            data-ajax-popup="true" data-title="{{ __('Contact Details') }}"
                                                            class="mx-3 btn btn-sm bg-warning align-items-center text-white ">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('Edit Contact')
                                                    <div class="action-btn  me-2">
                                                        <a href="{{ route('contact.edit', $contact->id) }}"data-size="md"
                                                            class="mx-3 btn bg-info btn-sm  align-items-center text-white "
                                                            data-bs-toggle="tooltip"data-title="{{ __('Contact Edit') }}"
                                                            title="{{ __('Edit') }}"><i class="ti ti-pencil"></i></a>
                                                    </div>
                                                @endcan
                                                @can('Delete Contact')
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $contact->id]]) !!}
                                                        <a href="#!"
                                                            class="mx-3 btn btn-sm  bg-danger align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip" title='Delete'>
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
