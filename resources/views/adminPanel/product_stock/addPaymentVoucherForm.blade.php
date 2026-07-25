<form action="{{route('admin.payment.store')}}" method="post">
    @csrf
    <div class="row">
        <div class="mb-2 row">
            <div class="col-sm-12">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Supplier Name
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <select name="supplier_id" id="supplier_id" class="form-control" onchange="loadSupplierCurrentBalance()">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $sRow)
                            <option value="{{$sRow->id}}">{{$sRow->supplier_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Bank Name
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <select name="bank_id" id="bank_id" class="form-control" onchange="togglePaymentOptions()">
                        <option value="">Select Bank</option>
                        @foreach($banks as $bRow)
                            <option value="{{$bRow->id}}">{{$bRow->account_name}} - {{$bRow->account_number}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Cash Name
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <select name="cash_id" id="cash_id" class="form-control" onchange="togglePaymentOptions()">
                        <option value="">Select Cash</option>
                        @foreach($cashs as $cRow)
                            <option value="{{$cRow->id}}">{{$cRow->account_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-sm-4">
                <strong>Total Purchase Amount</strong>
                <span id="totalPurchaseAmount"></span>
            </div>
            <div class="col-sm-4">
                <strong>Total Payment Amount</strong>
                <span id="totalPaymentAmount"></span>
            </div>
            <div class="col-sm-4">
                <strong>Current Balance</strong>
                <span id="currentPayableAmount"></span>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-sm-6">
                <label for="payment_date" class="col-sm-12  pr-0 col-form-label">Payment Date
                </label>
                <div class="col-sm-12">
                    <input type="date" id="payment_date" class="form-control"
                            name="payment_date"
                            placeholder="Payment Date" value="{{date('Y-m-d')}}">
                </div>
            </div>
            <div class="col-sm-6">
                <label for="amount" class="col-sm-12  pr-0 col-form-label">Payment Amount
                </label>
                <div class="col-sm-12">
                    <input type="number" id="amount" class="form-control" name="amount"
                            placeholder="Amount" step="any" min="0">
                </div>
            </div>
            <div class="col-sm-12">
                <label for="description" class="col-sm-12  pr-0 col-form-label">Description
                </label>
                <div class="col-sm-12">
                    <textarea name="description" class="form-control"
                                id="description" cols="10" rows="3"
                                placeholder="Description"></textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="d-flex justify-content-end p-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</form>
<script>
    function loadSupplierCurrentBalance(){
        var supplierId = $('#supplier_id').val();
        $.ajax({
            url: "{{route('admin.payment.loadSupplierCurrentBalance')}}",
            type: "get",
            data: {
                supplierId: supplierId
            },
            success: function (response) {
                $('#totalPurchaseAmount').text(response.totalPurchaseAmount);
                $('#totalPaymentAmount').text(response.totalPaymentAmount);
                var currentBalance = parseFloat(response.totalPurchaseAmount) - parseFloat(response.totalPaymentAmount);
                $('#currentPayableAmount').text(currentBalance);

            },
            error: function (xhr) {
            }
        });
    }
    function togglePaymentOptions() {
        if ($('#bank_id').val()) {
            $('#cash_id').prop('disabled', true);
        } else {
            $('#cash_id').prop('disabled', false);
        }

        if ($('#cash_id').val()) {
            $('#bank_id').prop('disabled', true);
        } else {
            $('#bank_id').prop('disabled', false);
        }
    }

    $(document).ready(function () {
        // Initial load
        loadSupplierCurrentBalance();
        togglePaymentOptions();

        // Event bindings
        $('#supplier_id').on('change', loadSupplierCurrentBalance);
        $('#bank_id').on('change', togglePaymentOptions);
        $('#cash_id').on('change', togglePaymentOptions);
    });
</script>