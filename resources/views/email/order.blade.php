<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif">

    @if ($mailData['userType'] == "customer")
    <h1>Thanks for your Order</h1>
    <h2>Your Order Id is #{{ $mailData['order']->id }}</h2>
    @else
    <h1>You have received an Order</h1>
    <h2>Order Id is #{{ $mailData['order']->id }}</h2>
    @endif

    <h2 class="h5 mb-3">Shipping Address</h2>
    <address>
        <strong>{{$mailData['order']->first_name.' '.$mailData['order']->last_name}}</strong><br>
        {{$mailData['order']->address}}<br>
        {{$mailData['order']->city}}, {{$mailData['order']->zip}} {{ getCountryName($mailData['order']->country_id)->name }} <br>
        Phone: {{$mailData['order']->mobile}}<br>
        Email: {{$mailData['order']->email}} <br>
        <strong>Shipped Date</strong> <br>
        @if (!empty($mailData['order']->shipped_date))
            {{\Carbon\Carbon::parse($mailData['order']->shipped_date)->format('d M, Y')}}
        @else
            N/A
        @endif
    </address>

    <h2>Products</h2>

    <table cellpadding="5" cellspacing="5" border="0" width="700">
        <thead>
            <tr background-color: "#CCC;">
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>                                        
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if ($mailData['order']->items->isNotEmpty())
                @foreach ($mailData['order']->items as $item)
                <tr>
                    <td> {{$item->name}} </td>
                    <td> {{$item->price}} </td>
                    <td> {{$item->qty}} </td>
                    <td> {{$item->total}} </td>
                </tr>
                    
                @endforeach
            @endif
            <tr>
                <th colspan="3" align="right">Subtotal:</th>
                <td>{{number_format($mailData['order']->sub_total,2);}}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Discount: {{ (!empty($mailData['order']->coupon_code) ? $mailData['order']->coupon_code : '') }} </th>
                <td>{{number_format($mailData['order']->discount,2);}}</td>
            </tr>
            
            <tr>
                <th colspan="3" align="right">Shipping:</th>
                <td>{{number_format($mailData['order']->shipping,2);}}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Grand Total:</th>
                <td>{{number_format($mailData['order']->grand_total,2);}}</td>
            </tr>
        </tbody>
    </table>
    
</body>
</html>