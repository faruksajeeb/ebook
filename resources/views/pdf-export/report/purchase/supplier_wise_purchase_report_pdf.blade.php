<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Invoice</title>
    <style>
           @page  {
        margin: 5em;
        size: A4; /*or width then height 150mm 50mm*/
        }
        * {
            font-family: 'Courier New', Courier, monospace;
        }

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

        .border-none {
            border: none;
        }

        .thead_label {
            font-weight: bold;
            background-color: #434141;
            color: #FFFFFF;
            padding: 5px;
        }
        #datatable{
            font-size:12px;
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

        <span style="background-color:#57375D; color:#FFFFFF;padding:5px;border-radius:5px">Supplier Wise Purchase Report</span>

    </div>
    <table style="width:100%; margin-top:50px" border="1" cellspacing="0" cellpadding="2">

        <tr>
            <td style="width:25%">Supplier Name:</td>
            <td style="width:25%" colspan="{{ $supplier->supplier_name == 'All' ? 2 : 1 }}">{{ $supplier->supplier_name }}</td>
            <td style="width:25%">Supplier Phone:</td>
            <td style="width:25%">{{ $supplier->supplier_phone }}</td>
        </tr>
        <tr>
            <td>Supplier Address:</td>
            <td colspan="{{ $supplier->supplier_name == 'All' ? 2 : 1 }}">{{ $supplier->supplier_address }}</td>
            <td>Date:</td>
            <td>{{ $date_range }}</td>
        </tr>
    </table>
    <br>
    <table id="datatable" width="100%" border="1" cellspacing="0" cellpadding="5">
        <thead>
            <th class="thead_label">Purchase Date</th>

            @if ($supplier->supplier_name == 'All')
                <th class="thead_label">Supplier Name</th>
            @endif

            <th class="thead_label">Total Amount</th>
            <th class="thead_label">Discount Perct.</th>
            <th class="thead_label">Discount Amt.</th>
            <th class="thead_label">Net Amount</th>
            <th class="thead_label">Pay Amount</th>
            <th class="thead_label">Due Amount</th>
        </thead>
        <tbody>
            @php
                $total = 0;
                $discountTotal = 0;
                $netTotal = 0;
                $payTotal = 0;
                $dueTotal = 0;
            @endphp
            @foreach ($purchases as $key => $purchase)
                <tr>
                    <td class="text-center">{{ $purchase->purchase_date }}</td>

                    @if ($supplier->supplier_name == 'All')
                        <td class="text-center">{{ $purchase->supplier->supplier_name }}</td>
                    @endif
                    <td class="text-right">{{ number_format($purchase->total_amount, 2) }}</td>
                    <td class="text-center">{{ $purchase->discount_percentage }}</td>
                    <td class="text-right">{{ $purchase->discount_amount }}</td>
                    <td class="text-right">{{ $purchase->net_amount }}</td>
                    <td class="text-right">{{ $purchase->pay_amount }}</td>
                    <td class="text-right">{{ $purchase->due_amount }}</td>
                </tr>
                @php
                    $total += $purchase->payment_amount;
                    $discountTotal += $purchase->discount_amount;
                    $netTotal += $purchase->net_amount;
                    $payTotal += $purchase->pay_amount;
                    $dueTotal += $purchase->due_amount;
                @endphp
            @endforeach
            <tr>
                <td colspan="{{ $supplier->supplier_name == 'All' ? 2 : 1 }}">Payment Total</td>
                
                <td class="text-right">{{ number_format($total, 2) }}</td>
                <td class="text-right"></td>
                <td class="text-right">{{ number_format($discountTotal, 2) }}</td>                
                <td class="text-right">{{ number_format($netTotal, 2) }}</td>
                <td class="text-right">{{ number_format($payTotal, 2) }}</td>
                <td class="text-right">{{ number_format($dueTotal, 2) }}</td>
            </tr>

        </tbody>
    </table>

</body>

</html>
