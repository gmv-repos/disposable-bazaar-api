@php
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
@endphp
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
                @foreach ($getSupplierLedger as $gslRow)
                    @php
                        // Update balanceAmount based on transaction type
                        if ($gslRow->transaction_type == 3) {
                            $balanceAmount += $gslRow->payable_amount;
                        } elseif ($gslRow->transaction_type == 4) {
                            $balanceAmount -= $gslRow->receipt_amount;
                        }
                        $totalPayments += $gslRow->payable_amount;
                        $totalReceipts += $gslRow->receipt_amount;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td class="text-center">{{ $gslRow->voucher_no }}</td>
                        <td class="text-center">{{ $gslRow->transaction_date }}</td>
                        <td class="text-right">{{ $gslRow->payable_amount }}</td>
                        <td class="text-right">{{ $gslRow->receipt_amount }}</td>
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
