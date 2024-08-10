@component('mail::message')

<div style="text-align: center; margin: 20px 0 15px;"> <img src="{{URL::asset('assets/images/invoice-logo.png')}}" width="150px" /> </div>

<div style="text-align: center; margin: 20px 0"> <strong style="font-size: 40px; font-size: 40px; letter-spacing: 5px;">{{$body['code']}} </strong></div>
<div style="text-align: center; margin: 0 0 50px;"> The code is your ToguMogu OTP Code. Please use it within 2 minutes.</div> 

Thanks,<br>
{{ config('app.full_name') }}
@endcomponent
