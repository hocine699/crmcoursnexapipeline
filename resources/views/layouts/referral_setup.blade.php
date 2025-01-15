<div class="card sticky-top" style="top:30px">
    <div class="list-group list-group-flush" id="useradd-sidenav">
        @if (\Auth::user()->type == 'super admin')
        <a href="{{ route('transaction.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('transaction*') ? 'active' : '' }}">{{ __('Transaction') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('payout_request.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('payout_request*') ? 'active' : '' }}">{{ __('Payout Request') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('referral_setting.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('referral_setting*') ? 'active' : '' }}">{{ __('Settings') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        @endif
        @if (\Auth::user()->type == 'owner')
        <a href="{{ route('referral_setting.guideline') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('guideline*') ? 'active' : '' }}">{{ __('Guideline') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('referral_transaction.company_index') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('referral_transaction*') ? 'active' : '' }}">{{ __('Referral Transaction') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        <a href="{{ route('company_payout') }}" class="list-group-item list-group-item-action border-0 {{ request()->is('company_payout*') ? 'active' : '' }}">{{ __('Payout') }}
            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
        </a>
        @endif
    </div>
</div>