@php
    $counter = 1;
    $balanceAmount = $balanceDetail->balanceAmount;
    $totalPayments = 0;
    $totalReceipts = 0;
@endphp
@foreach($getSupplierLedger as $gslRow)
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
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$gslRow->voucher_no}}</td>
        <td class="text-center">{{$gslRow->transaction_date}}</td>
        <td class="text-right">{{$gslRow->payable_amount}}</td>
        <td class="text-right">{{$gslRow->receipt_amount}}</td>
        <td class="text-right">{{$balanceAmount}}</td>
    </tr>
@endforeach
<tr>
    <th class="text-center" colspan="3">Total</th>
    <th class="text-right">{{$totalPayments}}</th>
    <th class="text-right">{{$totalReceipts}}</th>
    <th class="text-right">{{$balanceAmount}}</th>
</tr>