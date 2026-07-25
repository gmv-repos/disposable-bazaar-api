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