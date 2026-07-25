<form action="{{route('admin.receipt.store')}}" method="post">
    @csrf
    <div class="row">
        <div class="mb-2 row">
            <div class="col-sm-12">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Customer Name
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <select name="customer_id" id="customer_id" class="form-control" onchange="loadCustomerCurrentBalance()">
                        <option value="">Select Customer</option>
                        @foreach($customers as $cRow)
                            <option value="{{$cRow->id}}">{{$cRow->name}}</option>
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
                <strong>Total Sell Amount</strong>
                <span id="totalSellAmount"></span>
            </div>
            <div class="col-sm-4">
                <strong>Total Receipt Amount</strong>
                <span id="totalReceiptAmount"></span>
            </div>
            <div class="col-sm-4">
                <strong>Current Balance</strong>
                <span id="currentPayableAmount"></span>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-sm-6">
                <label for="receipt_date" class="col-sm-12  pr-0 col-form-label">Receipt Date
                </label>
                <div class="col-sm-12">
                    <input type="date" id="receipt_date" class="form-control"
                            name="receipt_date"
                            placeholder="Receipt Date" value="{{date('Y-m-d')}}">
                </div>
            </div>
            <div class="col-sm-6">
                <label for="amount" class="col-sm-12  pr-0 col-form-label">Receipt Amount
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
    function loadCustomerCurrentBalance(){
        var customerId = $('#customer_id').val();
        $.ajax({
            url: "{{route('admin.receipt.loadCustomerCurrentBalance')}}",
            type: "get",
            data: {
                customerId: customerId
            },
            success: function (response) {
                $('#totalSellAmount').text(response.totalSellAmount);
                $('#totalReceiptAmount').text(response.totalReceiptAmount);
                var currentBalance = parseFloat(response.totalSellAmount) - parseFloat(response.totalReceiptAmount);
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
        loadCustomerCurrentBalance();
        togglePaymentOptions();

        // Event bindings
        $('#customer_id').on('change', loadCustomerCurrentBalance);
        $('#bank_id').on('change', togglePaymentOptions);
        $('#cash_id').on('change', togglePaymentOptions);
    });
</script>