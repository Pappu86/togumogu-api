@component('mail::message')

Dear <strong> {{$body['customer_name']}}</strong>,

Your ToguMogu order status has been updated as {{$body['order_status']}}.
For further assistance please contact our customer care service {{$body['service_number']}}

Regards,<br>
{{ config('app.name') }}
@endcomponent
