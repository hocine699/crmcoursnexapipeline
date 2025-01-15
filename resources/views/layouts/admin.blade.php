@php
    $settings = \App\Models\Utility::settings();
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_favicon = Utility::getValByName('company_favicon');
    $users = \Auth::user();
    $currantLang = $users->currentLanguage();
    $languages = \App\Models\Utility::languages();
    $footer_text = isset($settings['footer_text']) ? $settings['footer_text'] : '';
    $setting = \App\Models\Utility::colorset();

    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $settings['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<meta name="csrf-token" content="{{ csrf_token() }}">

@include('partials.admin.head')

<body class="{{ $themeColor }}">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Mobile header ] End -->

    <!-- [ navigation menu ] start -->
    @include('partials.admin.menu')
    <!-- [ navigation menu ] end -->

    <!-- [ Header ] start -->
    @include('partials.admin.header')
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="page-content">
        @include('partials.admin.content')
    </div>
    <!-- [ Main Content ] end -->

    <footer class="dash-footer" id="footer-main">
        <div class="footer-wrapper">
            <div class="py-1">
                <span class="text-muted">&copy;
                    {{ date('Y') }}
                    {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'Salesy Saas') }}</span>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="commonModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelCommanModelLabel"></h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog" aria-labelledby="commonModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelCommanModelLabel"></h5>
                    <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    @include('partials.admin.footer')
    @include('Chatify::layouts.footerLinks')

    <!-- Apex Chart -->
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        feather.replace();
        var pctoggle = document.querySelector("#pct-toggler");
        if (pctoggle) {
            pctoggle.addEventListener("click", function() {
                if (!document.querySelector(".pct-customizer").classList.contains("active")) {
                    document.querySelector(".pct-customizer").classList.add("active");
                } else {
                    document.querySelector(".pct-customizer").classList.remove("active");
                }
            });
        }

        var themescolors = document.querySelectorAll(".themes-color > a");
        for (var h = 0; h < themescolors.length; h++) {
            var c = themescolors[h];

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }

        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }
        });



        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            if (custdarklayout.checked) {

                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('/storage/uploads/logo/logo-light.png') }}");
            } else {

                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('/storage/uploads/logo/logo-dark.png') }}");
            }
        });


        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>

</body>


</html>
