<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Purchase Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0 auto;
            font-size: 12px;
        }

        header {
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        #logo img {
            width: 100px;
            height: auto;
        }

        h2 {
            margin-top: 10px;
            color: #5D6975;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: bold;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            font-size: 11px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            color: #888;
        }
    </style>
</head>

<body>

    <header>
        <div id="logo">
            <img src="{{ public_path('Frontend/Assets/Logo.png') }}" alt="Company Logo">
        </div>
        <h2>Sell Profit</h2>
        <div>Date: {{ now()->format('d M Y') }}</div>
    </header>

    <main>
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center">S.NO.</th>
                    <th class="text-center">Voucher No</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Payment</th>
                    <th class="text-center">Receipt</th>
                    <th class="text-center">Balance</th>
                </tr>
            </thead>
            <tbody id="data">
                @php
                    $counter = 1;
                    $balanceAmount = $balanceDetail->balanceAmount;
                    $totalPayments = 0;
                    $totalReceipts = 0;
                @endphp
                @foreach ($getCustomerLedger as $gclRow)
                    @php
                        // Update balanceAmount based on transaction type
                        if ($gclRow->transaction_type == 1) {
                            $balanceAmount += $gclRow->receiveable_amount;
                        } elseif ($gclRow->transaction_type == 2) {
                            $balanceAmount -= $gclRow->receipt_amount;
                        }
                        $totalPayments += $gclRow->receiveable_amount;
                        $totalReceipts += $gclRow->receipt_amount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td class="text-center">{{ $gclRow->voucher_no }}</td>
                        <td class="text-center">{{ $gclRow->transaction_date }}</td>
                        <td class="text-right">{{ $gclRow->receiveable_amount }}</td>
                        <td class="text-right">{{ $gclRow->receipt_amount }}</td>
                        <td class="text-right">{{ $balanceAmount }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-center" colspan="3">Total</th>
                    <th class="text-right">{{ $totalPayments }}</th>
                    <th class="text-right">{{ $totalReceipts }}</th>
                    <th class="text-right">{{ $balanceAmount }}</th>
                </tr>
            </tbody>
        </table>
    </main>

</body>

</html>
