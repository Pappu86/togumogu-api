@component('mail::message')
@if ($body['is_customer_mail'])
<br/>
<!-- Start customer template content -->
@if ($body['type'] === 'book') 
<!-- Start customer book template content -->  
Dear {{$body['name']}},<br/>
Congratulations, You have successfully subscribed for the Book Box from us. Thank you for your subscription. One of our parent support executives will contact you shortly. For any further query call {{$body['support_mobile']}}.<br/>
Thank you so much for being a part of the Togumogu Family. We wish you all the best. Stay Safe!
<br /><br />
Regards, <br/>
The Togumogu team. 
<!-- End customer book template content -->
@elseif ($body['type'] === 'toy')
<!-- Start customer toy template content -->
Dear {{$body['name']}},<br/>
Congratulations, You have successfully subscribed for the toy Box from us. Thank you for your subscription. One of our parent support executives will contact you shortly. For any further query call {{$body['support_mobile']}}.<br/>
Thank you so much for being a part of the Togumogu Family. We wish you all the best. Stay Safe!
<br /><br />
Regards,<br/>
The Togumogu team.
<!-- End customer toy template content -->
@elseif ($body['type'] === 'nanny')
<!-- Start customer nanny template content -->
Dear {{$body['name']}},<br/>
Congratulations, You have successfully registered for our services. One of our parent support executives will contact you shortly.  For any further query call {{$body['support_mobile']}}.<br/>
Thank you for being a part of the ToguMogu Family. Share our app with others: <a href="https://togumogu.com/app" title="ToguMogu App">ToguMogu App</a>
<br /><br />
Regards,<br/>
The Togumogu team.
<!-- End customer nanny template content -->
@else
<!-- Start customer diaper template content -->
Dear {{$body['name']}},<br/>
Congratulations, You have successfully subscribed for the diaper from us. Thank you for your subscription. One of our parent support executives will contact you shortly. For any further query call {{$body['support_mobile']}}.<br/>
Thank you so much for being a part of the Togumogu Family. We wish you all the best. Stay Safe!
<br /><br />
Regards, <br/>
The Togumogu team. 
<!-- End customer diaper template content -->
@endif
<!-- End customer template content -->
<!-- Start togumogu template content -->
@else
# {{$body['subject']}}
Name: {{$body['name']}} <br>
Phone Number: {{$body['mobile']}} <br>
Email: {{$body['email']}} <br>
Name of the child: {{$body['child_name']}} <br>
Child DOB: {{$body['child_dob']}} <br>
@if ($body['type'] === 'diaper')
Preferred Brand: {{$body['brand']}} <br>
Current Size: {{$body['size']}} <br>
Pieces: {{$body['pieces']}} <br>
@elseif ($body['type'] === 'nanny')
How you want the service: {{$body['nanny_service']}} <br>
What is your budget for the service: {{$body['price_range']}} <br>
@else
How Frequently You want the {{$body['type']}} box: {{$body['frequent_want']}} <br>
What is the price range: {{$body['price_range']}} <br>
@endif
Address: {{$body['address']}}
<!-- End togumogu template content -->
@endif

@endcomponent
