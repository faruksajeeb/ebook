<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sale Invoice</title>
    <style>
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }
        .border-none{
            border:none;
        }
    </style>
</head>

<body>
    <div>
        <div style="width:15%; float:left">
            <img src="{{ public_path('assets/img/logo/logo.png') }}" width="100" height="100" alt="LOGO">
        </div>
        <div style="width:85%; float:right">
            <h1 style="text-align:center">Tritio Matra Publications</h1>
        </div>
    </div>
    <div class="text-center">
        
    <span style="background-color:#57375D; color:#FFFFFF;padding:5px;border-radius:5px">Sale Invoice</span>

    </div>
    <table style="width:100%; margin-top:50px" border="1" cellspacing="0" cellpadding="5">
        <tr>
            <td class="border-none" colspan="2">Invoice ID: {{ $sale->id }}</td>
            <td class="border-none text-right" colspan="2">Invoice Date: {{ $sale->sale_date }}</td>
        </tr>
        <tr>
            <td>Customer Name:</td>
            <td>{{ $sale->customer->customer_name }}</td>
            <td>Sale Note:</td>
            <td>{{ $sale->sale_note }}</td>
        </tr>
        <tr>
            <td>Shipping Address:</td>
            <td>{{ $sale->shipping_address }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <fieldset style="margin-top:50px">
        <legend>Regular Items</legend>
        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <th>SL No</th>
                <th>Book Title</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Price</th>
            </thead>
            <tbody>
                @foreach ($sale_regular_details as $key => $regularItem)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-left">{{ $regularItem->title }}</td>
                        <td class="text-right">{{ number_format($regularItem->price, 2) }}</td>
                        <td class="text-center">{{ $regularItem->quantity }}</td>
                        <td class="text-right">{{ number_format($regularItem->sub_total, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">Total</td>
                    <td class="text-right">{{ $sale->total_amount }}</td>
                </tr>
                <tr>
                    <td colspan="3">Discount</td>
                    <td class="text-center">{{$sale->discount_percentage}}%</td>
                    <td class="text-right">{{ $sale->discount_amount }}</td>
                </tr>
                <tr>
                    <td colspan="3">Vat</td>
                    <td class="text-center">{{$sale->vat_percentage}}%</td>
                    <td class="text-right">{{ $sale->vat_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4">Shipping Cost</td>
                    <td class="text-right">{{ $sale->shipping_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4">Net Total</td>
                    <td class="text-right">{{ $sale->net_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4">Pay Amount</td>
                    <td class="text-right">{{ $sale->pay_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4">Due Amount</td>
                    <td class="text-right">{{ $sale->due_amount }}</td>
                </tr>
            </tbody>
        </table>
    </fieldset>

    <fieldset style="margin-top:50px">
        <legend>Courtesy Items</legend>
        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <th>SL No</th>
                <th>Book Title</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Price</th>
            </thead>
            <tbody>
                @foreach ($sale_courtesy_details as $key => $courtesyItem)
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-left">{{ $courtesyItem->title }}</td>
                        <td class="text-right">{{ number_format($courtesyItem->unit_price, 2) }}</td>
                        <td class="text-center">{{ $courtesyItem->courtesy_quantity }}</td>
                        <td class="text-right">{{ number_format($courtesyItem->sub_total, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">Total</td>
                    <td class="text-right">{{ $sale->courtesy_total_amount }}</td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</body>

</html>
