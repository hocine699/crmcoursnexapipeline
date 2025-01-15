<div class="col-lg-12 order-lg-1">
    <dl class="row">
        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Name') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->name }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Website') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->website }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Email') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->email }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Phone') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->phone }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Billing Address') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->billing_address }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('City') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->billing_city }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Country') }}</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ $account->billing_country }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Type') }}</span></dt>
        <dd class="col-sm-8"><span
                class="text-sm">{{ !empty($account->accountType) ? $account->accountType->name : '-' }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Industry') }}</span></dt>
        <dd class="col-sm-8"><span
                class="text-sm">{{ !empty($account->accountIndustry) ? $account->accountIndustry->name : '-' }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span></dt>
        <dd class="col-sm-8"><span
                class="text-sm">{{ !empty($account->assign_user) ? $account->assign_user->name : '-' }}</span></dd>

        <dt class="col-sm-4"><span class="h6 text-sm mb-0">Created</span></dt>
        <dd class="col-sm-8"><span class="text-sm">{{ \Auth::user()->dateFormat($account->created_at) }}</span></dd>
    </dl>
</div>

</div>
<div class="w-100 text-end pr-2">
    @can('Edit Account')
        <div class="action-btn">
            <a href="{{ route('account.edit', $account->id) }}"
                data-bs-toggle="tooltip"class="mx-3 btn btn-sm bg-info align-items-center text-white "
                data-title="{{ __('Account Edit') }}" title="{{ __('Edit') }}"><i class="ti ti-pencil"></i>
            </a>
        </div>
    @endcan
</div>
</div>
