<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>S.NO.</th>
            <th>Voucher No</th>
            <th>Date</th>
            <th>Payment</th>
            <th>Receipt</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @php
            $counter = 1;
            $balanceAmount = $balanceDetail->balanceAmount;
            $totalPayments = 0;
            $totalReceipts = 0;
        @endphp
        @foreach ($getSupplierLedger as $gslRow)
            @php
                if ($gslRow->transaction_type == 3) {
                    $balanceAmount += $gslRow->payable_amount;
                } elseif ($gslRow->transaction_type == 4) {
                    $balanceAmount -= $gslRow->receipt_amount;
                }
                $totalPayments += $gslRow->payable_amount;
                $totalReceipts += $gslRow->receipt_amount;
            @endphp
            <tr>
                <td>{{ $counter++ }}</td>
                <td>{{ $gslRow->voucher_no }}</td>
                <td>{{ $gslRow->transaction_date }}</td>
                <td>{{ $gslRow->payable_amount }}</td>
                <td>{{ $gslRow->receipt_amount }}</td>
                <td>{{ $balanceAmount }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3">Total</th>
            <th>{{ $totalPayments }}</th>
            <th>{{ $totalReceipts }}</th>
            <th>{{ $balanceAmount }}</th>
        </tr>
    </tbody>
</table>
