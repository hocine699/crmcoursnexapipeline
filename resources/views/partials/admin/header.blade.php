@php
    $users = \Auth::user();
    // $profile = asset(Storage::url('upload/profile/'));
    $profile = \App\Models\Utility::get_file('upload/profile/');
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $lang = isset($users->lang) ? $users->lang : 'en';
    if ($lang == null) {
        $lang = 'en';
    }
    $LangName = \App\Models\Languages::where('code', $lang)->first();
    if (empty($LangName)) {
        $LangName  = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
@endphp

@if (isset($settings['cust_theme_bg']) && $settings['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg">
    @else
        <header class="dash-header">
@endif
<div class="header-wrapper">
    <div class="me-auto dash-mob-drp">
        <ul class="list-unstyled">
            <li class="dash-h-item mob-hamburger">
                <a href="#" class="nav-link nav-link-icon sidenav-toggler" data-action="sidenav-pin"
                    data-target="#sidenav-main"></a>
                <a href="#!" class="dash-head-link" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
            </li>
            <li class="dropdown dash-h-item drp-company">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-target="#sidenav-main"
                    data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="theme-avtar">
                        @php
                            $profile = \App\Models\Utility::get_file('upload/profile/');

                        @endphp

                        @if (\Request::route()->getName() == 'chats')
                            <img class="rounded border border-primary border-2"
                                src="{{ !empty($users->avatar) ? $users->avatar : 'avatar.png' }}" style="th:100%; height:100%;">
                        @else
                            <img class="rounded border border-primary border-2"
                                src="{{ $profile }}{{ !empty($users->avatar) ? $users->avatar : 'avatar.png' }}"
                                style="th:100%; height:100%;">
                        @endif
                    </span>
                    <span class="hide-mob ms-2">{{ __('Hi') }}, {{ $users->name }}</span>
                    <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                </a>

                <div class="dropdown-menu dash-h-dropdown">

                    <a href="{{ route('profile') }}" class="dropdown-item">
                        <i class="ti ti-user"></i>
                        <span>{{ __('Profile') }}</span>
                    </a>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                        class="dropdown-item">
                        <i class="ti ti-power"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        </ul>
    </div>

    <div class="ms-auto">
        <ul class="list-unstyled">
            @if (\Auth::user()->type != 'super admin')
            @impersonating($guard = null)
						<li class="dropdown dash-h-item drp-company">
								<a class="btn btn-danger btn-sm me-3"
								    href="{{ route('exit.company') }}"><i class="ti ti-ban"></i>
								    {{ __('Exit Company Login') }}
								</a>
						</li>
				@endImpersonating
                <li class="dash-h-item">
                    <a href="{{ url('chats') }}" class="dash-head-link me-0">
                        <i class="ti ti-message-circle " style="font-size: 21px"></i>
                        <span
                            class="bg-danger dash-h-badge message-counter custom_messanger_counter">{{ $unseenCounter }}<span
                                class="sr-only"></span>
                        </span>
                    </a>
                </li>
            @endif
            <li class="dropdown dash-h-item drp-language">
                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ti ti-world nocolor"></i>
                    <span
                        class="drp-text hide-mob">{{ ucFirst(isset($LangName->fullName) ? $LangName->fullName : 'en') }}</span>
                    <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                </a>
                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                    @foreach (App\Models\Utility::languages() as $code => $lang)
                        <a href="{{ route('change.language', $code) }}"
                            class="dropdown-item {{ $currantLang == $code ? 'text-primary' : '' }}">
                            <span>{{ ucFirst($lang) }}</span>
                        </a>
                    @endforeach
                    @if (Auth::user()->type == 'super admin')
                        <a href="#" data-url="{{ route('create.language') }}"
                            class="dropdown-item border-top py-1 text-primary" data-bs-toggle="tooltip"
                            data-ajax-popup="true" data-title="{{ __('Create New Language') }}">
                            <span>{{ __('Create Languages') }}</span>
                        </a>
                        <a href="{{ route('manage.language', [$currantLang]) }}"
                            class="dropdown-item border-top py-1 text-primary">
                            <span>{{ __('Manage Languages') }}</span>
                        </a>
                    @endif
                </div>
            </li>
        </ul>
    </div>
</div>
</header>
