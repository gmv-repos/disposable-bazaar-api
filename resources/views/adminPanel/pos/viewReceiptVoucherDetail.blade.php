<div class="well" id="PrintReceiptVoucherDetail">
    <style>
        .floatLeft{
            width: 48%;
            float: left;
        }
        .floatRight{
            width: 48%;
            float: right;
        }
        @media print {
            th {
                font-size: 10px;
                /* Additional styling for print, if needed */
                color: #000; /* Example: ensure text color is black for print */
            }
            td {
                font-size: 9px;
                /* Additional styling for print, if needed */
                color: #000; /* Example: ensure text color is black for print */
            }
        }
    </style>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            <h3>Receipt Voucher Detail</h3>
        </div>
        <div class="lineHeight">&nbsp;</div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                    <tr>
                                        <th colspan="2" class="text-center">Customer Detail</th>
                                    </tr>
                                    <tr>
                                        <th>Customer Name</th>
                                        <td>{{$receiptDetail->name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone No</th>
                                        <td>{{$receiptDetail->phone}}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{$receiptDetail->email}}</td>
                                    </tr>
                                    @if($receiptDetail->bank_id != 0)
                                    <tr>
                                        <th>Bank Name</th>
                                        <td>{{$receiptDetail->bank_name}}</td>
                                    </tr>
                                    @endif

                                    @if($receiptDetail->cash_id != 0)
                                    <tr>
                                        <th>Cash Name</th>
                                        <td>{{$receiptDetail->cash_name}}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td>
                                        <strong>Address:</strong>
                                        <p>{{$receiptDetail->address ?? '-'}}</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="floatRight">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                    <tr>
                                        <th colspan="2" class="text-center">Receipt Detail</th>
                                    </tr>
                                    <tr>
                                        <th>Receipt No</th>
                                        <td>{{$receiptDetail->voucher_no}}</td>
                                    </tr>
                                    <tr>
                                        <th>Receipt Date</th>
                                        <td>{{$receiptDetail->receipt_date}}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>{{$receiptDetail->amount}}</td>
                                    </tr>
                                    
                                    @if($receiptDetail->bank_id != 0)
                                    <tr>
                                        <th>Account Number</th>
                                        <td>{{$receiptDetail->account_number}}</td>
                                    </tr>
                                    @endif

                                    @if($receiptDetail->cash_id != 0)
                                    <tr>
                                        <th>Account Number</th>
                                        <td>-</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td>
                                        <strong>Description:</strong>
                                        <p>{{$receiptDetail->description ?? '-'}}</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <th>Overall Sell Amount</th>
                                <td id="totalSellAmountV"></td>
                                <th>Overall Receipt Amount</th>
                                <td id="totalReceiptAmountV"></td>
                                <th>Current Balance</th>
                                <td id="currentPayableAmountV"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="lineHeight">&nbsp;</div>
        <div class="lineHeight">&nbsp;</div>
        <div class="lineHeight">&nbsp;</div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            &nbsp;
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-1">&nbsp;</div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p style="border-bottom: 1px solid #000;"><strong>Receiver Signature</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function loadCustomerCurrentBalance(){
        var customerId = '{{$receiptDetail->customer_id}}';
        $.ajax({
            url: "{{route('admin.receipt.loadCustomerCurrentBalance')}}",
            type: "get",
            data: {
                customerId: customerId
            },
            success: function (response) {
                $('#totalSellAmountV').text(response.totalSellAmount);
                $('#totalReceiptAmountV').text(response.totalReceiptAmount);
                var currentBalance = parseFloat(response.totalSellAmount) - parseFloat(response.totalReceiptAmount);
                $('#currentPayableAmountV').text(currentBalance);

            },
            error: function (xhr) {
            }
        });
    }
    loadCustomerCurrentBalance();
</script>