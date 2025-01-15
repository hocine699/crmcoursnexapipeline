 @extends('layouts.admin')
 @section('page-title')
     {{ __('Invoice') }}
 @endsection
 @section('title')
     {{ __('Invoice') }}
 @endsection
 @section('breadcrumb')
     <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
     <li class="breadcrumb-item">{{ __('Invoice') }}</li>
 @endsection
 @section('action-btn')
     <div class="action-btn me-2">
         <a href="{{ route('invoice.export') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
             data-title=" {{ __('Export') }}" title=" {{ __('Export') }}">
             <i class="ti ti-file-export"></i>
         </a>
     </div>

     @can('Create Invoice')
         <div class="action-btn me-2">
             <a href="#" data-size="lg" data-url="{{ route('invoice.create', ['invoice', 0]) }}" data-ajax-popup="true"
                 data-bs-toggle="tooltip" data-title="{{ __('Create New Invoice') }}" title=" {{ __('Create') }}"
                 class="btn btn-sm btn-primary btn-icon m-1">
                 <i class="ti ti-plus"></i>
             </a>
         </div>
     @endcan
 @endsection
 @section('filter')
 @endsection

 @section('content')
     <div class="row">
         <div class="col-xl-12">
             <div class="card">
                 <div class="card-body table-border-style">
                     <div class="table-responsive">
                         <table class="table datatable" id="datatable">
                             <thead>
                                 <tr>
                                     <th scope="col" class="sort" data-sort="id">{{ __('ID') }}</th>
                                     <th scope="col" class="sort" data-sort="name">{{ __('Name') }}</th>
                                     <th scope="col" class="sort" data-sort="budget">{{ __('Account') }}</th>
                                     <th scope="col" class="sort" data-sort="status">{{ __('Status') }}</th>
                                     <th scope="col" class="sort" data-sort="completion">{{ __('Created At') }}
                                     </th>
                                     <th scope="col" class="sort" data-sort="completion">{{ __('Amount') }}</th>
                                     <th scope="col" class="sort" data-sort="completion">{{ __('Assigned User') }}
                                     </th>
                                     @if (Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                                         <th scope="col" class="text-end">{{ __('Action') }}</th>
                                     @endif
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($invoices as $invoice)
                                     <tr>
                                         <td>
                                             <a href="{{ route('invoice.edit', $invoice->id) }}"
                                                 class="btn btn-outline-primary" data-title="{{ __('Quote Details') }}">
                                                 {{ \Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}
                                             </a>
                                         </td>
                                         <td>
                                             <span class="budget">
                                                 {{ ucfirst($invoice->name) }}

                                             </span>
                                         </td>
                                         <td>
                                             <span class="budget">
                                                 {{ ucfirst(!empty($invoice->accounts) ? $invoice->accounts->name : '--') }}</span>
                                         </td>
                                         <td>
                                             @if ($invoice->status == 0)
                                                 <span class="badge bg-secondary p-2 px-3"
                                                     style="width: 91px;">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                             @elseif($invoice->status == 1)
                                                 <span class="badge bg-danger p-2 px-3"
                                                     style="width: 91px;">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                             @elseif($invoice->status == 2)
                                                 <span class="badge bg-warning p-2 px-3"
                                                     style="width: 91px;">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                             @elseif($invoice->status == 3)
                                                 <span class="badge bg-success p-2 px-3"
                                                     style="width: 91px;">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                             @elseif($invoice->status == 4)
                                                 <span class="badge bg-info p-2 px-3"
                                                     style="width: 91px;">{{ __(\App\Models\Invoice::$status[$invoice->status]) }}</span>
                                             @endif
                                         </td>
                                         <td>
                                             <span
                                                 class="budget">{{ \Auth::user()->dateFormat($invoice->created_at) }}</span>
                                         </td>
                                         <td>
                                             <span
                                                 class="budget">{{ \Auth::user()->priceFormat($invoice->getTotal()) }}</span>
                                         </td>
                                         <td>
                                             <span
                                                 class="budget">{{ ucfirst(!empty($invoice->assign_user) ? $invoice->assign_user->name : '-') }}</span>
                                         </td>
                                         @if (Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice'))
                                             <td class="text-end">
                                                 @can('Create Invoice')
                                                     <div class="action-btn  me-2">
                                                         {!! Form::open([
                                                             'method' => 'get',
                                                             'route' => ['invoice.duplicate', $invoice->id],
                                                             'id' => 'duplicate-form-' . $invoice->id,
                                                         ]) !!}

                                                         <a href="#"
                                                             class="mx-3 btn btn-sm bg-success align-items-center text-white  duplicate_confirm"
                                                             data-bs-toggle="tooltip" title="{{ __('Duplicate') }}"
                                                             data-toggle="tooltip" data-original-title="{{ __('Delete') }}"
                                                             data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"
                                                             data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">
                                                             <i class="ti ti-copy"></i>
                                                             {!! Form::close() !!}
                                                         </a>
                                                     </div>
                                                 @endcan
                                                 @can('Show Invoice')
                                                     <div class="action-btn  me-2">
                                                         <a href="{{ route('invoice.show', $invoice->id) }}"
                                                             data-bs-toggle="tooltip" title="{{ __('Details') }}"
                                                             class="mx-3 btn btn-sm  bg-warning align-items-center text-white "
                                                             data-title="{{ __('Invoice Details') }}">
                                                             <i class="ti ti-eye"></i>
                                                         </a>
                                                     </div>
                                                 @endcan
                                                 @can('Edit Invoice')
                                                     <div class="action-btn  me-2">
                                                         <a href="{{ route('invoice.edit', $invoice->id) }}"
                                                             data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                             class="mx-3 btn btn-sm bg-info align-items-center text-white "
                                                             data-title="{{ __('Edit Invoice') }}"><i
                                                                 class="ti ti-pencil"></i></a>
                                                     </div>
                                                 @endcan
                                                 @can('Delete Invoice')
                                                     <div class="action-btn">
                                                         {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id]]) !!}
                                                         <a href="#!"
                                                             class="mx-3 btn btn-sm bg-danger align-items-center text-white show_confirm"
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
 @push('script-page')
     <script>
         $(document).on('click', '#billing_data', function() {
             $("[name='shipping_address']").val($("[name='billing_address']").val());
             $("[name='shipping_city']").val($("[name='billing_city']").val());
             $("[name='shipping_state']").val($("[name='billing_state']").val());
             $("[name='shipping_country']").val($("[name='billing_country']").val());
             $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
         })

         $(document).on('change', 'select[name=opportunity]', function() {

             var opportunities = $(this).val();
             console.log(opportunities);
             getaccount(opportunities);
         });

         function getaccount(opportunities_id) {
             $.ajax({
                 url: '{{ route('invoice.getaccount') }}',
                 type: 'POST',
                 data: {
                     "opportunities_id": opportunities_id,
                     "_token": "{{ csrf_token() }}",
                 },
                 success: function(data) {
                     console.log(data);
                     $('#amount').val(data.opportunitie.amount);
                     $('#account_name').val(data.account.name);
                     $('#account_id').val(data.account.id);
                     $('#billing_address').val(data.account.billing_address);
                     $('#shipping_address').val(data.account.shipping_address);
                     $('#billing_city').val(data.account.billing_city);
                     $('#billing_state').val(data.account.billing_state);
                     $('#shipping_city').val(data.account.shipping_city);
                     $('#shipping_state').val(data.account.shipping_state);
                     $('#billing_country').val(data.account.billing_country);
                     $('#billing_postalcode').val(data.account.billing_postalcode);
                     $('#shipping_country').val(data.account.shipping_country);
                     $('#shipping_postalcode').val(data.account.shipping_postalcode);

                 }
             });
         }
     </script>
 @endpush
