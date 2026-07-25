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
                    <th class="text-center">Out</th>
                    <th class="text-center">In</th>
                    <th class="text-center">Extra Out</th>
                    <th class="text-center">Extra In</th>
                    <th class="text-center">Balance</th>
                </tr>
            </thead>
            <tbody id="data">
                @php
                    $counter = 1;
                    $balanceAmount =
                        ($balanceDetail->totalReceiptAmount ?? 0)
                        - ($balanceDetail->totalPaidAmount ?? 0)
                        - ($balanceDetail->totalExpenseAmount ?? 0)
                        + ($balanceDetail->totalAmountIn ?? 0)
                        - ($balanceDetail->totalAmountOut ?? 0)
                        + ($balanceDetail->totalExtraIn ?? 0)
                        - ($balanceDetail->totalExtraOut ?? 0);

                    $totalPayments = 0;
                    $totalReceipts = 0;
                    $totalAmountOut = 0;
                    $totalAmountIn = 0;
                    $totalExtraOut = 0;
                    $totalExtraIn = 0;
                @endphp

                @foreach($getBankLedger as $gblRow)
                    @php
                        $paymentAmount = 0;
                        $receiptAmount = 0;
                        $amountOut = 0;
                        $amountIn = 0;
                        $extraOutAmount = 0;
                        $extraInAmount = 0;

                        switch ($gblRow->transaction_type) {
                            case 2:
                                $receiptAmount = $gblRow->receipt_amount;
                                $balanceAmount += $receiptAmount;
                                $totalReceipts += $receiptAmount;
                                break;

                            case 4:
                                $paymentAmount = $gblRow->receipt_amount;
                                $balanceAmount -= $paymentAmount;
                                $totalPayments += $paymentAmount;
                                break;

                            case 5:
                                $paymentAmount = $gblRow->expense_amount;
                                $balanceAmount -= $paymentAmount;
                                $totalPayments += $paymentAmount;
                                break;

                            case 21:
                                $amountIn = $gblRow->amount_in;
                                $balanceAmount += $amountIn;
                                $totalAmountIn += $amountIn;
                                break;

                            case 22:
                                $amountOut = $gblRow->amount_out;
                                $balanceAmount -= $amountOut;
                                $totalAmountOut += $amountOut;
                                break;

                            case 23:
                                $extraInAmount = $gblRow->extra_trx_amount;
                                $balanceAmount += $extraInAmount;
                                $totalExtraIn += $extraInAmount;
                                break;

                            case 24:
                                $extraOutAmount = $gblRow->extra_trx_amount;
                                $balanceAmount -= $extraOutAmount;
                                $totalExtraOut += $extraOutAmount;
                                break;
                        }
                    @endphp

                    <tr>
                        <td class="text-center"> {{ $counter++ }}</td>
                        <td class="text-center">{{ $gblRow->voucher_no }}</td>
                        <td class="text-center">{{ $gblRow->transaction_date }}</td>
                        <td class="text-right">{{ $paymentAmount == 0 ? '' : number_format($paymentAmount, 2) }}</td>
                        <td class="text-right">{{ $receiptAmount == 0 ? '' : number_format($receiptAmount, 2) }}</td>
                        <td class="text-right">{{ $amountOut == 0 ? '' : number_format($amountOut, 2) }}</td>
                        <td class="text-right">{{ $amountIn == 0 ? '' : number_format($amountIn, 2) }}</td>
                        <td class="text-right">{{ $extraOutAmount == 0 ? '' : number_format($extraOutAmount, 2) }}</td>
                        <td class="text-right">{{ $extraInAmount == 0 ? '' : number_format($extraInAmount, 2) }}</td>
                        <td class="text-right">{{ $balanceAmount == 0 ? '' : number_format($balanceAmount, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <th class="text-center" colspan="3">Total</th>
                    <th class="text-right">{{ number_format($totalPayments, 2) }}</th>
                    <th class="text-right">{{ number_format($totalReceipts, 2) }}</th>
                    <th class="text-right">{{ number_format($totalAmountOut, 2) }}</th>
                    <th class="text-right">{{ number_format($totalAmountIn, 2) }}</th>
                    <th class="text-right">{{ number_format($totalExtraOut, 2) }}</th>
                    <th class="text-right">{{ number_format($totalExtraIn, 2) }}</th>
                    <th class="text-right">{{ number_format($balanceAmount, 2) }}</th>
                </tr>
            </tbody>
        </table>
    </main>

</body>

</html>