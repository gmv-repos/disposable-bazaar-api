@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.vouchers.inOutToAccountStore') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="transactionType" class="form-label">
                                Transaction Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="transactionType" id="transactionType" class="form-control" required>
                                <option value="">Transaction Type</option>
                                <option value="in">Credit</option>
                                <option value="out">Debit</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="accountType" class="form-label">
                                Account Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="accountType" id="accountType" class="form-control"
                                onchange="filterAccountsByType()" required>
                                <option value="1">Bank</option>
                                <option value="2">Cash</option>
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label for="accountId" class="form-label">
                                Account <strong class="text-danger">*</strong>
                            </label>

                            <select name="accountId" id="accountId" class="form-control"
                                onchange="loadAccountCurrentBalance()" required>
                                <option value="" selected>Select Account</option>
                                @foreach ($accounts as $aRow)
                                    <option value="{{ $aRow->id }}" accountType={{ $aRow->type }}>
                                        {{ $aRow->account_name }} - {{ $aRow->account_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="row mx-1 my-3">
                        <div class="col-sm-12 d-flex justify-content-between mx-0 border p-3">
                            <strong>Current Balance</strong>
                            <span id="currentBalance">0</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label for="amount" class="form-label">
                                Amount
                            </label>
                            <input type="number" id="amount" class="form-control" name="amount" placeholder="Amount"
                                step="any" min="0" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="date" class="form-label">
                                Date
                            </label>
                            <input type="date" id="date" class="form-control" name="date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea name="description" class="form-control" id="description" cols="10" rows="3"
                                placeholder="Description"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary mt-4 px-5">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            filterAccountsByType();
        });

        function filterAccountsByType() {
            const selectedType = $('#accountType').val();

            $('#accountId option').each(function() {
                const accountType = $(this).attr('accountType');
                if (accountType === selectedType) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('#accountId').val('');
            $('#currentBalance').text('0.00');
        }


        function loadAccountCurrentBalance() {
            var accountId = $('#accountId').val();
            $.ajax({
                url: "{{ route('admin.vouchers.loadAccountCurrentBalance') }}",
                type: "GET",
                data: {
                    accountId: accountId
                },
                success: function(response) {

                    $('#currentBalance').text(response.currentBalance);
                },
                error: function(xhr) {
                    console.error("Failed to load account balance", xhr);
                }
            });
        }
    </script>
@endsection
