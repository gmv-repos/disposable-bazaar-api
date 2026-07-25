<h6 class="h6">Party Ledger Report</h6>
<p>Date Range: {{ $fromDate }} to {{ $toDate }}</p>

<table class="table table-bordered" id="partyLedgerReport">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Receivable</th>
            <th>Payable</th>
            <th>Receipt</th>
            <th>Remarks</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="6"><strong>Opening Balance</strong></td>
            <td><strong>{{ number_format($openingBalance, 2) }}</strong></td>
        </tr>
        @foreach($ledgerEntries as $entry)
            <tr>
                <td>{{ $entry->transaction_date }}</td>
                <td>
                    @php
                        $types = [1 => 'Sell', 2 => 'Receipt', 3 => 'Purchase', 4 => 'Payment'];
                    @endphp
                    {{ $types[$entry->transaction_type] ?? $entry->transaction_type }}
                </td>
                <td>{{ number_format($entry->receiveable_amount, 2) }}</td>
                <td>{{ number_format($entry->payable_amount, 2) }}</td>
                <td>{{ number_format($entry->receipt_amount, 2) }}</td>
                <td>{{ $entry->remarks }}</td>
                <td>{{ number_format($entry->running_balance, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="6"><strong>Closing Balance</strong></td>
            <td><strong>{{ number_format($closingBalance, 2) }}</strong></td>
        </tr>
    </tbody>
</table>