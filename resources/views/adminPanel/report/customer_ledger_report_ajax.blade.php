@php
    $counter = 1;
    $balanceAmount = $balanceDetail->balanceAmount;
    $totalPayments = 0;
    $totalReceipts = 0;
@endphp
@foreach($getCustomerLedger as $gclRow)
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
        <td class="text-center">{{$counter++}}</td>
        <td class="text-center">{{$gclRow->voucher_no}}</td>
        <td class="text-center">{{$gclRow->transaction_date}}</td>
        <td class="text-right">{{$gclRow->receiveable_amount}}</td>
        <td class="text-right">{{$gclRow->receipt_amount}}</td>
        <td class="text-right">{{$balanceAmount}}</td>
    </tr>
@endforeach
<tr>
    <th class="text-center" colspan="3">Total</th>
    <th class="text-right">{{$totalPayments}}</th>
    <th class="text-right">{{$totalReceipts}}</th>
    <th class="text-right">{{$balanceAmount}}</th>
</tr>