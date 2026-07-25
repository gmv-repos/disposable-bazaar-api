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