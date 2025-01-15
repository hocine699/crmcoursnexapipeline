 @php
    $logo=asset(Storage::url('uploads/logo'));
    $company_favicon=Utility::getValByName('company_favicon');
    $seo_setting = App\Models\Utility::getSeoSetting();

@endphp
 <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Salesy Saas- Business Sales CRM">
    <meta name="author" content="Rajodiya Infotech">
    <title>{{(Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'SalesGo')}} - @yield('page-title')</title>
      <!-- Primary Meta Tags -->

      <meta name="title" content="{{$seo_setting['meta_keywords']}}">
      <meta name="description" content="{{$seo_setting['meta_description']}}">

      <!-- Open Graph / Facebook -->
      <meta property="og:type" content="website">
      <meta property="og:url" content="{{env('APP_URL')}}">
      <meta property="og:title" content="{{$seo_setting['meta_keywords']}}">
      <meta property="og:description" content="{{$seo_setting['meta_description']}}">
      <meta property="og:image" content="{{asset('uploads/metaevent/'.$seo_setting['meta_image'])}}">

      <!-- Twitter -->
      <meta property="twitter:card" content="summary_large_image">
      <meta property="twitter:url" content="{{env('APP_URL')}}">
      <meta property="twitter:title" content="{{$seo_setting['meta_keywords']}}">
      <meta property="twitter:description" content="{{$seo_setting['meta_description']}}">
      <meta property="twitter:image" content="{{asset('uploads/metaevent/'.$seo_setting['meta_image'])}}">
    <link rel="icon" href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" type="image" sizes="16x16">
    <meta name="csrf-token" content="{{ csrf_token() }}">
 </head>
 @php
    $plan_id= \Illuminate\Support\Facades\Crypt::decrypt($data['plan_id']);
    $plandata=App\Models\Plan::find($plan_id);
 @endphp




  <script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"> </script>
  <div id="payment-form-container"> </div>
  <script>

    var  paymentwall_callback = "{{ url('/plan/paymentwall') }}";

    var brick = new Brick({
      public_key: '{{ $admin_payment_setting['paymentwall_public_key']  }}', // please update it to Brick live key before launch your project
      amount: {{$plandata->price}},
      currency: '{{ $admin_payment_setting['currency']  }}',
      container: 'payment-form-container',
      action: '{{route("paymentwall.payment",[$data["plan_id"],"coupon" => $data["coupon"]])}}',
      success_url:'{{route("plan.index")}}',
      form: {
        merchant: 'Paymentwall',
        product: '{{$plandata->name}}',
        pay_button: 'Pay',
        show_zip: true, // show zip code
        show_cardholder: true // show card holder name
      },



    });

    brick.showPaymentForm(function(data) {
      if(data.flag == 1){
        window.location.href ='{{route("error.plan.show",1)}}';
      }else{
        window.location.href ='{{route("error.plan.show",2)}}';
      }
    }, function(errors) {
      if(errors.flag == 1){
        window.location.href ='{{route("error.plan.show",1)}}';
      }else{
        window.location.href ='{{route("error.plan.show",2)}}';
      }
    });

  </script>
