
<html>
<body>
    <div style="max-width: 564px; margin: 0 auto; padding:15px; background: #f7f7f7; border-radius:3px;">
        <div style="margin: 0px; display: flex; padding: 20px 0px; ">
            <div style="padding: 15px 15px 0 0;">
                <img alt="Invoice logo" width="200" src="{{asset('assets/images/invoice-logo.png')}}" />
            </div>
            <div style="width: 230px; margin-left: auto;">
                <strong> ToguMogu Pvt. Limited </strong> <br />
                Rezina Garden, House 67/A, Road 9/A, <br />
                Dhanmondi, Dhaka, Bangladesh <br />
                +88-0194-4665577
            </div>
        </div>
        <h1 style="margin-bottom: 10px;">Invoice</h1>
        <div style="margin: 0; display: flex; padding: 0 0 30px 0;" >
            <div style="padding: 0 15px 0 0; max-width: 310px;">
                <strong> {{$body['customer_name']}}</strong> <br />
                Contact Number:
                <strong> {{$body['customer_contact_number']}}</strong> <br />
                Address:
                <strong>{{$body['order_delivery_address']}}</strong><br />
                Payment Status:
                <strong> {{$body['payment_status']}}</strong> <br />
                Special note:
                <strong> {{$body['order_special_note']}}</strong>
                <br />
            </div>
            <div style="width: 230px; margin-left: auto;">
                Order Date : {{$body['order_date']}} <br />
                Order Number : {{$body['order_no']}} <br />
                Invoice Number : {{$body['invoice_no']}} <br />
                Payment Method : {{$body['order_payment_method']}}
                <br />
                Total quantity : {{$body['total_quantity']}}
            </div>
        </div>
        <table CELLSPACING="0" style="margin-bottom: 20px; width: 100%;">
            <thead>
                <tr> 
                    <th style="text-align: left; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                    #
                    </th>
                    <th style="text-align: left; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Product
                    </th>
                    <th style="text-align: right; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Quantity
                    </th>
                    <th style="text-align: right; height: 40px; width: 100px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Unit Price
                    </th>
                    <th style="text-align: right; height: 40px; width: 100px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Total
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($body['products'] as $product)
                <tr>
                    <td style="height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                    {{ $loop->index + 1 }}
                    </td>
                    <td style="height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                        {{ $product->product->name }}
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                    {{ $product->quantity }}
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    ৳{{ $product->selling_unit_price }}

                    @if ($product->selling_unit_price !== $product->regular_unit_price)
                    <br/>
                    <small> <del style="opacity: 0.5">{{ $product->regular_unit_price }}</del></small>
                    @endif

                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                    ৳{{ $product->quantity * $product->selling_unit_price }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;">
                    <strong>Sub-Total</strong>
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    ৳{{ $body['sub_total_amount'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Discount
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    -৳{{ $body['coupon_discount'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Delivery cost
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    ৳ {{ $body['shipping_cost'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    <strong>Total</strong>
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    ৳{{ $body['total_amount'] }}
                    </td>
                </tr>
                @if ($body['coupon_discount'] > 0 || $body['special_discount'] > 0)
                <tr>
                    <td colspan="4" style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    Total save
                    </td>
                    <td style="text-align: right; height: 40px; border-bottom: 1px solid #d5d5d5; background: #FFFFFF; padding: 0 16px;" >
                    ৳{{ $body['coupon_discount'] + $body['special_discount'] }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        <div style="margin: 0px; display: flex; text-align: center; align-items: center; padding: 20px 0;" >
            <div style="padding: 15px">
                <h3 style="margin-bottom: 10px"> Thank you for shopping with us. </h3>
            </div>
        </div>
    </div>
</body>
</html>