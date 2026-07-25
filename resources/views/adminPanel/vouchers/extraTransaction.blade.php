@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><strong>There were some problems with your input:</strong></h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header text-center pt-4">
                <h5 class="h5">Extra Transactions</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.vouchers.extraTransactionStore')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="voucher_type" class="form-label">
                                Voucher Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="voucher_type" id="voucher_type" class="form-control">
                                <option value="">Select Voucher Type</option>
                                <option value="23">Receipt</option>
                                <option value="24">Payment</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="party_id" class="form-label">
                                Party <strong class="text-danger">*</strong>
                            </label>
                            <select name="party_id" id="party_id" class="form-control">
                                <option value="">Select Party</option>
                                @foreach($parties as $sRow)
                                    <option value="{{$sRow->id}}">{{$sRow->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <div class="row mx-1 my-3">
                        <div class="col-sm-12 d-flex justify-content-between border p-3 mx-0">
                            <strong>Balance</strong>
                            <span id="balance">0</span>
                        </div>
                    </div> --}}
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label for="accountType" class="form-label">
                                Account Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="accountType" id="accountType" class="form-control"
                                onchange="filterAccountsByType()" required>
                                <option value="1">Bank</option>
                                <option value="2">Cash</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label for="account_id" class="form-label">
                                Account <strong class="text-danger">*</strong>
                            </label>

                            <select name="account_id" id="account_id" class="form-control" required>
                                <option value="" selected>Select Account</option>
                                @foreach($accounts as $aRow)
                                    <option value="{{$aRow->id}}" accountType={{ $aRow->type }}>
                                        {{$aRow->account_name}} - {{$aRow->account_number}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label for="amount" class="form-label">
                                Voucher Amount
                            </label>
                            <input type="number" id="amount" class="form-control" name="amount" placeholder="Amount"
                                step="any" min="0">
                        </div>
                        <div class="col-sm-6">
                            <label for="voucher_date" class="form-label">
                                Voucher Date
                            </label>
                            <input type="date" id="voucher_date" class="form-control" name="voucher_date"
                                value="{{ date('Y-m-d') }}">
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

        $(document).ready(function () {
            filterAccountsByType();
        });

        function filterAccountsByType() {
            const selectedType = $('#accountType').val();

            $('#account_id option').each(function () {
                const accountType = $(this).attr('accountType');
                if (accountType === selectedType) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $('#account_id').val('');
        }


        function loadPartyExtraBalance() {
            var partyId = $('#party_id').val();
            $.ajax({
                url: "{{ route('admin.vouchers.loadPartyExtraBalance') }}",
                type: "GET",
                data: {
                    partyId: partyId
                },
                success: function (response) {

                    $('#balance').text(response.balance);
                },
                error: function (xhr) {
                }
            });
        }

    </script>
@endsection