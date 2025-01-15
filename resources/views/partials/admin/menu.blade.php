@php
// $logo=\App\Models\Utility::get_file('uploads/logo/');
$logo = asset('storage/uploads/logo/');

$company_logo = \App\Models\Utility::GetLogo();

$users = \Auth::user();
$currantLang = $users->currentLanguage();
$emailTemplate = App\Models\EmailTemplate::getemailtemplate();
$defaultView = App\Models\UserDefualtView::select('module','route')->where('user_id', $users->id)->get()->pluck('route', 'module')->toArray();
@endphp
@if ((isset($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on'))
<nav class="dash-sidebar light-sidebar transprent-bg">
    @else
    <nav class="dash-sidebar light-sidebar">
        @endif
        <div class="navbar-wrapper">
            <div class="m-header main-logo">
                <a href="{{ route('dashboard') }}" class="b-brand">
                    <!-- ========   change your logo hear   ============ -->
                    {{-- <img src="{{ asset(Storage::url('logo/'.$logo)) }}" alt="{{ env('APP_NAME') }}" class="logo logo-lg" />
                    <img src="{{ asset(Storage::url('logo/'.$logo)) }}" alt="{{ env('APP_NAME') }}" class="logo logo-sm" /> --}}
                    <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') .'?'.time()}}" alt="{{ config('app.name', 'Salesy') }}" class="logo logo-lg nav-sidebar-logo" />
                </a>
            </div>

            <div class="navbar-content">
                <ul class="dash-navbar">
                    <li class="dash-item {{ \Request::route()->getName() == 'dashboard' ? ' active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-home-2"></i></span><span class="dash-mtext">{{ __('Dashboard') }}</span></a>
                    </li>

                    @can('Manage Role')
                    <li class="dash-item {{ \Request::route()->getName() == 'role' ? ' active' : '' }}">
                        <a href="{{ route('role.index') }}" class="dash-link"><span class="dash-micon">
                                <i class="ti ti-license"></i></span><span class="dash-mtext">{{ __('Role') }}</span></a>
                    </li>
                    @endcan

                    @if(\Auth::user()->type == 'super admin')
                    @can('Manage User')
                    <li class="dash-item {{ \Request::route()->getName() == 'user' || \Request::route()->getName() == 'user.edit' ? ' active' : '' }}">
                        {{-- <a class="dash-link" href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('user')) ? route(\Auth::user()->getDefualtViewRouteByModule('user')) : route('user.index') }}"> --}}
                        <a class="dash-link" href="{{ array_key_exists('user',$defaultView) ? route($defaultView['user']) : route('user.index') }}">
                            <span class="dash-micon"><i class="ti ti-user"></i></span><span class="dash-mtext">{{ __('Companies') }}</span></a>
                    </li>
                    @endcan
                    @else
                    @can('Manage User')
                    <li class="dash-item {{ \Request::route()->getName() == 'user' || \Request::route()->getName() == 'user.edit' ? ' active' : '' }}">
                        {{-- <a class="dash-link" href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('user')) ? route(\Auth::user()->getDefualtViewRouteByModule('user')) : route('user.index') }}"> --}}
                        <a class="dash-link" href="{{ array_key_exists('user',$defaultView) ? route($defaultView['user']) : route('user.index') }}">
                            <span class="dash-micon"><i class="ti ti-user"></i></span><span class="dash-mtext">{{ __('User') }}</span></a>
                    </li>
                    @endcan
                    @endif

                    @if (\Auth::user()->type != 'super admin')
                    <li class="dash-item {{ \Request::route()->getName() == 'messages' ? ' active' : '' }}">
                        <a href="{{ url('chats') }}" class="dash-link {{ Request::segment(1) == 'messages' ? 'active' : '' }}">
                            <span class="dash-micon"><i class="ti ti-brand-messenger"></i></span><span class="dash-mtext">{{ __('Messenger') }}</span>
                        </a>
                    </li>
                    @endif

                    @if(\Auth::user()->type == 'owner')
                    <li class="dash-item {{ \Request::route()->getName() == 'notification_templates' ? 'active' : ''}}">
                        <a class="dash-link" href={{url('notification-templates')}}>
                            <span class="dash-micon"><i class="ti ti-notification"></i></span><span class="dash-mtext">{{ __('Notification Template') }}</span></a>
                    </li>
                    @endif

                    @can('Manage Form Builder')
                    <li class="dash-item  {{ \Request::route()->getName() == 'form_builder' || \Request::route()->getName() == 'form_builder.show' || \Request::route()->getName() == 'form.response' ? ' active' : '' }}">
                        <a href="{{ route('form_builder.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-align-justified"></i></span><span class="dash-mtext">{{ __('Form Builder') }}</span>
                        </a>
                    </li>
                    @endcan

                    @can('Manage Account')
                    <li class="dash-item {{ \Request::route()->getName() == 'account' || \Request::route()->getName() == 'account.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('account')) ? route(\Auth::user()->getDefualtViewRouteByModule('account')) : route('account.index') }}" class="dash-link">
                        <span class="dash-micon"> <i class="ti ti-building"></i></span><span class="dash-mtext">{{ __('Accounts') }}</span>
                        </a> --}}
                        <a href="{{array_key_exists('account',$defaultView) ? route($defaultView['account']) : route('account.index') }}" class="dash-link">
                            <span class="dash-micon"> <i class="ti ti-building"></i></span><span class="dash-mtext">{{ __('Accounts') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Contact')
                    <li class="dash-item {{ \Request::route()->getName() == 'contact' || \Request::route()->getName() == 'contact.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('contact')) ? route(\Auth::user()->getDefualtViewRouteByModule('contact')) : route('contact.index') }}" class="dash-link ">
                        <span class="dash-micon"><i class="ti ti-file-phone"></i></span><span class="dash-mtext">{{ __('Contacts') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('contact',$defaultView) ? route($defaultView['contact']) : route('contact.index') }}" class="dash-link ">
                            <span class="dash-micon"><i class="ti ti-file-phone"></i></span><span class="dash-mtext">{{ __('Contacts') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Lead')
                    <li class="dash-item {{ \Request::route()->getName() == 'lead' || \Request::route()->getName() == 'lead.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('lead')) ? route(\Auth::user()->getDefualtViewRouteByModule('lead')) : route('lead.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-filter"></i></span><span class="dash-mtext">{{ __('Leads') }}</span>
                        </a> --}}
                        <a href="{{  array_key_exists('lead',$defaultView) ? route($defaultView['lead']) : route('lead.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-filter"></i></span><span class="dash-mtext">{{ __('Leads') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Opportunities')
                    <li class="dash-item {{ \Request::route()->getName() == 'opportunities' || \Request::route()->getName() == 'opportunities.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('opportunities')) ? route(\Auth::user()->getDefualtViewRouteByModule('opportunities')) : route('opportunities.index') }}"
                        class="dash-link">
                        <span class="dash-micon"><i class="ti ti-currency-dollar-singapore"></i></span><span class="dash-mtext">{{ __('Opportunities') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('opportunities',$defaultView) ? route($defaultView['opportunities']) : route('opportunities.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-currency-dollar-singapore"></i></span><span class="dash-mtext">{{ __('Opportunities') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Product')
                    <li class="dash-item {{ \Request::route()->getName() == 'product' || \Request::route()->getName() == 'product.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('product')) ? route(\Auth::user()->getDefualtViewRouteByModule('product')) : route('product.index') }}"
                        class="dash-link">
                        <span class="dash-micon"><i class="ti ti-brand-producthunt"></i></span><span class="dash-mtext">{{ __('Products') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('product',$defaultView) ? route($defaultView['product']) : route('product.index')}}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-brand-producthunt"></i></span><span class="dash-mtext">{{ __('Products') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Quote')
                    <li class="dash-item {{ \Request::route()->getName() == 'quote' || \Request::route()->getName() == 'quote.show' || \Request::route()->getName() == 'quote.edit' ? ' active' : '' }}">
                        <a href="{{ route('quote.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-blockquote"></i></span><span class="dash-mtext">{{ __('Quotes') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage SalesOrder')
                    <li class="dash-item {{ \Request::route()->getName() == 'salesorder' || \Request::route()->getName() == 'salesorder.show' || \Request::route()->getName() == 'salesorder.edit' ? ' active' : '' }}">
                        <a href="{{ route('salesorder.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-invoice"></i></span><span class="dash-mtext">{{ __('Sales Orders') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Invoice')
                    <li class="dash-item {{ \Request::route()->getName() == 'invoice' || \Request::route()->getName() == 'invoice.show' || \Request::route()->getName() == 'invoice.edit' ? ' active' : '' }}">
                        <a href="{{ route('invoice.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-receipt"></i></span><span class="dash-mtext">{{ __('Invoices') }}</span>
                        </a>
                    </li>
                    @endcan
                    @if (\Auth::user()->type != 'super admin')
                    @can('Manage Invoice Payment')
                    <li class="dash-item {{ \Request::route()->getName() == 'invoices-payments' ? ' active' : '' }}">
                        <a class="dash-link " href="{{ route('invoices.payments') }} ">
                            <span class="dash-micon"><i class="ti ti-credit-card"></i></span><span class="dash-mtext">{{ __('Payment') }}</span>
                        </a>
                    </li>
                    @endcan
                    @endif

                    @can('Manage CommonCase')
                    <li class="dash-item {{ \Request::route()->getName() == 'commoncases' || \Request::route()->getName() == 'commoncases.show' || \Request::route()->getName() == 'commoncases.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('commoncases')) ? route(\Auth::user()->getDefualtViewRouteByModule('commoncases')) : route('commoncases.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-briefcase"></i></span><span class="dash-mtext">{{ __('Cases') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('commoncases',$defaultView) ? route($defaultView['commoncases']) : route('commoncases.index')}}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-briefcase"></i></span><span class="dash-mtext">{{ __('Cases') }}</span>
                        </a>
                    </li>
                    @endcan
                    @if(\Auth::user()->type!='super admin')
                    <li class="dash-item {{ \Request::route()->getName() == 'calendar' || \Request::route()->getName() == 'calendar.index' ? ' active' : '' }}">
                        <a href="{{ route('calendar.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="far fa-calendar-alt"></i></span><span class="dash-mtext">{{ __('Calendar') }}</span>
                        </a>
                    </li>
                    @endif
                    @can('Manage Task')
                    <li class="dash-item {{ \Request::route()->getName() == 'task' || \Request::route()->getName() == 'task.show' || \Request::route()->getName() == 'task.edit' || \Request::route()->getName() == 'task.gantt.chart' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('task')) ? route(\Auth::user()->getDefualtViewRouteByModule('task')) : route('task.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="fas fa-tasks"></i></span><span class="dash-mtext">{{ __('Task') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('task',$defaultView) ? route($defaultView['task']) : route('task.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="fas fa-tasks"></i></span><span class="dash-mtext">{{ __('Task') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Meeting')
                    <li class="dash-item {{ \Request::route()->getName() == 'meeting' || \Request::route()->getName() == 'meeting.show' || \Request::route()->getName() == 'meeting.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('meeting')) ? route(\Auth::user()->getDefualtViewRouteByModule('meeting')) : route('meeting.index') }}"
                        class="dash-link">
                        <span class="dash-micon"><i class="ti ti-calendar"></i></span><span class="dash-mtext">{{ __('Meeting') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('meeting',$defaultView) ? route($defaultView['meeting']) : route('meeting.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-calendar"></i></span><span class="dash-mtext">{{ __('Meeting') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Call')
                    <li class="dash-item {{ \Request::route()->getName() == 'call' || \Request::route()->getName() == 'call.show' || \Request::route()->getName() == 'call.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('call')) ? route(\Auth::user()->getDefualtViewRouteByModule('call')) : route('call.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-phone-call"></i></span><span class="dash-mtext">{{ __('Call') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('call',$defaultView) ? route($defaultView['call']) : route('call.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-phone-call"></i></span><span class="dash-mtext">{{ __('Call') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Contract')
                    <li class="dash-item  {{ (Request::route()->getName() == 'contract.index' || Request::route()->getName() == 'contract.show') ? 'active' : '' }}">
                        <a href="{{route('contract.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-device-floppy"></i></span><span class="dash-mtext">{{__('Contracts')}}</span></a>
                    </li>
                    @endcan
                    @can('Manage Document')
                    <li class="dash-item {{ \Request::route()->getName() == 'document' || \Request::route()->getName() == 'document.show' || \Request::route()->getName() == 'document.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('document')) ? route(\Auth::user()->getDefualtViewRouteByModule('document')) : route('document.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-file-analytics"></i></span><span class="dash-mtext">{{ __('Document') }}</span>
                        </a> --}}
                        <a href="{{ array_key_exists('document',$defaultView) ? route($defaultView['document']) : route('document.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-file-analytics"></i></span><span class="dash-mtext">{{ __('Document') }}</span>
                        </a>
                    </li>
                    @endcan
                    @can('Manage Campaign')
                    <li class="dash-item {{ \Request::route()->getName() == 'campaign' || \Request::route()->getName() == 'campaign.show' || \Request::route()->getName() == 'campaign.edit' ? ' active' : '' }}">
                        {{-- <a href="{{ !empty(\Auth::user()->getDefualtViewRouteByModule('campaign')) ? route(\Auth::user()->getDefualtViewRouteByModule('campaign')) : route('campaign.index') }}" class="dash-link">
                        <span class="dash-micon"><i class="ti ti-chart-line"></i></span><span class="dash-mtext">{{ __('Campaigns') }}</span>
                        </a> --}}
                        <a href="{{  array_key_exists('campaign',$defaultView) ? route($defaultView['campaign']) : route('campaign.index')  }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-chart-line"></i></span><span class="dash-mtext">{{ __('Campaigns') }}</span>
                        </a>
                    </li>
                    @endcan
                    @if (\Auth::user()->type != 'super admin')
                    <li class="dash-item {{ \Request::route()->getName() == 'stream' ? ' active' : '' }}">
                        <a href="{{ route('stream.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-rss"></i></span><span class="dash-mtext">{{ __('Stream') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'super admin' || \Auth::user()->type == 'owner')
                    <li class="dash-item {{ \Request::route()->getName() == 'plan' || \Request::route()->getName() == 'plan.show' || \Request::route()->getName() == 'plan.payment' || \Request::route()->getName() == 'plan.edit' ? ' active' : '' }}">
                        <a href="{{ route('plan.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext">{{ __('Plan') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'owner')
                    <li class="dash-item  {{ \Request::route()->getName() == 'referral_setting.guideline' ? ' active' : '' }}">
                        <a href="{{ route('referral_setting.guideline') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-discount-2"></i></span><span class="dash-mtext">{{ __('Referral Program') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'super admin')
                    <li class="dash-item  {{ \Request::route()->getName() == 'plan_request' || \Request::route()->getName() == 'plan_request.show' || \Request::route()->getName() == 'plan_request.edit' ? ' active' : '' }}">
                        <a href="{{ route('plan_request.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-brand-telegram"></i></span><span class="dash-mtext">{{ __('Plan Request') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'super admin')
                    <li class="dash-item  {{ \Request::route()->getName() == 'transaction' || \Request::route()->getName() == 'referral_setting' || \Request::route()->getName() == 'payout_request' ? ' active' : '' }}">
                        <a href="{{ route('transaction.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-discount-2"></i></span><span class="dash-mtext">{{ __('Referral Program') }}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'super admin')
                    <li class="dash-item {{ \Request::route()->getName() == 'coupon' || \Request::route()->getName() == 'coupon.show' ? ' active' : '' }}">
                        <a href="{{ route('coupon.index') }}" class="dash-link">
                            <span class="dash-micon"> <i class="ti ti-briefcase"></i></span><span class="dash-mtext">{{ __('Coupon') }}</span></a>
                        </a>
                    </li>
                    @endif

                    @if (\Auth::user()->type == 'super admin' || \Auth::user()->type == 'owner')
                    <li class="dash-item {{ \Request::route()->getName() == 'order' ? ' active' : '' }}">
                        <a href="{{ route('order.index') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-shopping-cart-plus"></i></span><span class="dash-mtext">{{ __('Order') }}</span>
                        </a>
                    </li>
                    @endif

                    @if (\Auth::user()->type == 'super admin')
                    <li class="dash-item {{ (Request::route()->getName() == 'email_template.index' || Request::segment(1) == 'email_template_lang' || Request::route()->getName() == 'manageemail.lang') ? 'active' : '' }}">
                        <a href="{{ route('email_template.index',[$emailTemplate ->id,\Auth::user()->lang]) }}" class="dash-link"><span class="dash-micon"><i class="ti ti-template"></i></span><span class="dash-mtext">{{ __('Email Template') }}</span></a>
                    </li>
                    @endif

                    @if (Gate::check('Manage Report'))
                    <li class="dash-item dash-hasmenu  {{ \Request::route()->getName() == 'report.index' || \Request::route()->getName() == 'report.show' || \Request::route()->getName() == 'report.edit' ? ' active dash-trigger' : '' }}">
                        <a class="dash-link collapsed">
                            <span class="dash-micon"><i class="ti ti-trending-up"></i></span>{{ __('Reports') }}<span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                        </a>

                        <ul class="dash-submenu">
                            <li class="dash-item {{ \Request::route()->getName() == 'report.index' || \Request::route()->getName() == 'report.show' || \Request::route()->getName() == 'report.edit' ? ' active ' : '' }}">
                                <a href="{{ route('report.index') }}" class="dash-link">
                                    {{ __('Custom Report') }}</a>
                            </li>
                            @can('Manage Report')
                            <li class="dash-item {{ \Request::route()->getName() == 'report.leadsanalytic' ? ' active ' : '' }}">
                                <a href="{{ route('report.leadsanalytic') }}" class="dash-link">
                                    {{ __('Leads Analytics') }}</a>
                            </li>
                            @endcan
                            @can('Manage Report')
                            <li class="dash-item {{ \Request::route()->getName() == 'leadsanalytic' ? ' active ' : '' }}">
                                <a href="{{ route('report.invoiceanalytic') }}" class="dash-link">
                                    {{ __('Invoice Analytics') }}</a>
                            </li>
                            @endcan
                            @can('Manage Report')
                            <li class="dash-item {{ \Request::route()->getName() == 'salesorderanalytic' ? ' active ' : '' }}">
                                <a href="{{ route('report.salesorderanalytic') }}" class="dash-link">
                                    {{ __('Sales Order Analytics') }}</a>
                            </li>
                            @endcan
                            @can('Manage Report')
                            <li class="dash-item  {{ \Request::route()->getName() == 'quoteanalytic' ? ' active ' : '' }}">
                                <a href="{{ route('report.quoteanalytic') }}" class="dash-link">
                                    {{ __('Quote Analytics') }}</a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                    @endif

                    @if (Gate::check('Manage AccountType') || Gate::check('Manage AccountIndustry') || Gate::check('Manage LeadSource') || Gate::check('Manage OpportunitiesStage') || Gate::check('Manage CaseType') || Gate::check('Manage DocumentType') || Gate::check('Manage DocumentFolder') || Gate::check('Manage TargetList') || Gate::check('Manage CampaignType') || Gate::check('Manage ProductCategory') || Gate::check('Manage ProductBrand') || Gate::check('Manage ProductTax') || Gate::check('Manage ShippingProvider') || Gate::check('Manage TaskStage') || Gate::check('Manage ContractType') || Gate::check('Manage Tax'))
                    <li class="dash-item dash-hasmenu">
                        <a class="dash-link collapsed {{ \Request::route()->getName() == 'account_type' || \Request::route()->getName() == 'account_industry' || Request::segment(1) == 'lead_source' || Request::segment(1) == 'opportunities_stage' || \Request::route()->getName() == 'case_type' || Request::route()->getName() == 'document_folder' || Request::route()->getName() == 'document_type' || Request::route()->getName() == 'target_list' || Request::route()->getName() == 'campaign_type' || Request::route()->getName() == 'product_category' || Request::segment(1) == 'product_brand' || Request::segment(1) == 'product_tax' || Request::route()->getName() == 'shipping_provider' || Request::route()->getName() == 'task_stage' || Request::route()->getName() == 'contract_type' || Request::route()->getName() == 'taxes' || Request::route()->getName() == 'payments' ? 'true' : 'false' }}">
                            <span class="dash-micon"><i class="ti ti-circle-square"></i></span><span class="dash-mtext">{{ __('Constant') }}</span><span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                        <ul class="dash-submenu">
                            @if (Gate::check('Manage AccountType') || Gate::check('Manage AccountIndustry'))
                            <li class="dash-item dash-hasmenu">
                                <a class="dash-link">{{ __('Account') }}<span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('Manage AccountType')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'account_type' ? 'active open' : '' }}" href="{{ route('account_type.index') }}">{{ __('Type') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage AccountIndustry')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'account_industry' ? 'active open' : '' }}" href="{{ route('account_industry.index') }}">{{ __('Industry') }}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                            @endif

                            @if (Gate::check('Manage DocumentType') || Gate::check('Manage DocumentFolder'))
                            <li class="dash-item dash-hasmenu">
                                <a href="#!" class="dash-link">{{ __('Document') }}<span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('Manage DocumentFolder')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'document_folder' ? 'active open' : '' }}" href="{{ route('document_folder.index') }}">{{ __('Folder') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage DocumentType')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'document_type' ? 'active open' : '' }}" href="{{ route('document_type.index') }}">{{ __('Type') }}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                            @endif

                            @if (Gate::check('Manage TargetList') || Gate::check('Manage CampaignType'))
                            <li class="dash-item dash-hasmenu">
                                <a href="#!" class="dash-link">{{ __('Campaign') }}<span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('Manage TargetList')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'target_list' ? 'active open' : '' }}" href="{{ route('target_list.index') }}">{{ __('Target Lists') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage CampaignType')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'campaign_type' ? 'active open' : '' }}" href="{{ route('campaign_type.index') }}">{{ __('Type') }}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                            @endif

                            @if (Gate::check('Manage ProductCategory') || Gate::check('Manage ProductBrand') || Gate::check('Manage ProductTax'))
                            <li class="dash-item dash-hasmenu">
                                <a href="#!" class="dash-link">{{ __('Product') }}<span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                                <ul class="dash-submenu">
                                    @can('Manage ProductCategory')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'product_category' ? 'active open' : '' }}" href="{{ route('product_category.index') }}">{{ __('Category') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage ProductBrand')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'product_brand' ? 'active open' : '' }}" href="{{ route('product_brand.index') }}">{{ __('Brand') }}</a>
                                    </li>
                                    @endcan
                                    @can('Manage ProductTax')
                                    <li class="dash-item">
                                        <a class="dash-link {{ Request::route()->getName() == 'product_tax' ? 'active open' : '' }}" href="{{ route('product_tax.index') }}">{{ __('Tax') }}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                            @endif



                            @if (\Auth::user()->type == 'owner' || \Auth::user()->type == 'Manager')
                            @if (Gate::check('Manage ContractType'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'contract_type' ? 'active open' : '' }}" href="{{route('contract_type.index')}}">{{__('Contract Type')}}</a>
                            </li>
                            @endif
                            @endif



                            @if (Gate::check('Manage LeadSource'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'lead_source' ? 'active open' : '' }}" href="{{ route('lead_source.index') }}">{{ __('Lead Source') }}</a>
                            </li>
                            @endif

                            @if (Gate::check('Manage OpportunitiesStage'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'opportunities_stage' ? 'active open' : '' }}" href="{{ route('opportunities_stage.index') }}">{{ __('Opportunity Stage') }}</a>
                            </li>
                            @endif

                            @if (Gate::check('Manage CaseType'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'case_type' ? 'active open' : '' }}" href="{{ route('case_type.index') }}">{{ __('Case Type') }}</a>
                            </li>
                            @endif

                            @if (Gate::check('Manage ShippingProvider'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'shipping_provider' ? 'active open' : '' }}" href="{{ route('shipping_provider.index') }}">{{ __('Shipping Provider') }}</a>
                            </li>
                            @endif

                            @if (Gate::check('Manage TaskStage'))
                            <li class="dash-item">
                                <a class="dash-link {{ Request::route()->getName() == 'task_stage' ? 'active open' : '' }}" href="{{ route('task_stage.index') }}">{{ __('Task Stage') }}</a>
                            </li>
                            @endif


                        </ul>
                    </li>
                    @endif
                    @if (\Auth::user()->type == 'super admin')
                    @include('landingpage::menu.landingpage')
                    @endif
                    @if (\Auth::user()->type == 'super admin' || \Auth::user()->type == 'owner')
                    <li class="dash-item  {{ Request::route()->getName() == 'settings' ? 'active' : '' }}">
                        <a href="{{ route('settings') }}" class="dash-link">
                            <span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">{{ __('Settings') }}</span>
                        </a>
                    </li>
                    @endif


                </ul>
            </div>
        </div>
    </nav>
