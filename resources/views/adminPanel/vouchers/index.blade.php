@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.vouchers.voucherStore')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="voucher_type" class="form-label">
                                Voucher Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="voucher_type" id="voucher_type" class="form-control">
                                <option value="">Select Voucher Type</option>
                                <option value="payment">Payment</option>
                                <option value="receipt">Receipt</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="party_id" class="form-label">
                                Party <strong class="text-danger">*</strong>
                            </label>
                            <select name="party_id" id="party_id" class="form-control" onchange="loadPartyBalance()">
                                <option value="">Select Party</option>
                                @foreach($parties as $sRow)
                                    <option value="{{$sRow->id}}">{{$sRow->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mx-1 my-3">
                        <div class="col-sm-12 d-flex justify-content-between border p-3 mx-0">
                            <strong>Balance</strong>
                            <span id="balance">0</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <label for="account_type" class="form-label">
                                Account Type <strong class="text-danger">*</strong>
                            </label>
                            <select name="account_type" id="account_type" class="form-control"
                                onchange="filterAccountsByType()">
                                <option value="bank">Bank</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="account_id" class="form-label">
                                Account <strong class="text-danger">*</strong>
                            </label>

                            <select name="account_id" id="account_id" class="form-control">
                                @foreach($banks as $bRow)
                                    <option value="{{$bRow->id}}" data-type="bank">
                                        {{$bRow->account_name}} - {{$bRow->account_number}}
                                    </option>
                                @endforeach
                                @foreach($cashs as $cRow)
                                    <option value="{{$cRow->id}}" data-type="cash">
                                        {{$cRow->account_name}}
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

        let allAccountOptions;

        $(document).ready(function () {
            allAccountOptions = $('#account_id option').clone();
        });

        function filterAccountsByType() {
            let selectedType = $('#account_type').val();
            let $accountSelect = $('#account_id');

            $accountSelect.empty();

            $accountSelect.append('<option value="">Select Account</option>');

            allAccountOptions.each(function () {
                let type = $(this).data('type');
                if (!type || type === selectedType) {
                    $accountSelect.append($(this));
                }
            });
        }


        function loadPartyBalance() {
            var partyId = $('#party_id').val();
            $.ajax({
                url: "{{ route('admin.vouchers.loadPartyBalance') }}",
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

        $(document).ready(function () {

            loadPartyBalance();
        });
    </script>
@endsection