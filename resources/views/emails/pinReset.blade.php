@component('mail::message')

<div style="text-align: center; margin: 20px 0 15px;"> <img src="{{URL::asset('assets/images/invoice-logo.png')}}" width="150px" /> </div>

<div style="text-align: center; margin: 20px 0"> <strong style="font-size: 40px; font-size: 40px; letter-spacing: 5px;">{{$body['code']}} </strong></div>
<div style="text-align: center; margin: 0 0 50px;">  This code is your current PIN. Now you can use it for login. We suggest you change your PIN from settings for better security.</div> 

Thanks,<br>
{{ config('app.full_name') }}
@endcomponent
