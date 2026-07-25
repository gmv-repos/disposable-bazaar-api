<div class="well" id="PrintReceiptVoucherDetail">
    <style>
        .floatLeft {
            width: 48%;
            float: left;
        }

        .floatRight {
            width: 48%;
            float: right;
        }

        @media print {
            th {
                font-size: 10px;
                /* Additional styling for print, if needed */
                color: #000;
                /* Example: ensure text color is black for print */
            }

            td {
                font-size: 9px;
                /* Additional styling for print, if needed */
                color: #000;
                /* Example: ensure text color is black for print */
            }
        }
    </style>
    <form action="{{ route('admin.addReturnInvoiceStore') }}" method="post">
        <input type="text" name="invoice_id" id="invoice_id" value="{{ $selldata->id }}" />
        @csrf
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <h3>Return Invoice Form</h3>
            </div>
            <div class="lineHeight">&nbsp;</div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="floatLeft">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table-bordered table-condensed table">
                                    <tbody>
                                        <tr>
                                            <th colspan="2" class="text-center">Customer Detail</th>
                                        </tr>
                                        <tr>
                                            <th>Customer Name</th>
                                            <td>{{ $selldata->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $selldata->customer->address ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone No</th>
                                            <td>{{ $selldata->customer->phone ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="floatRight">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table-bordered table-condensed table">
                                    <tbody>
                                        <tr>
                                            <th colspan="2" class="text-center">Invoice Detail</th>
                                        </tr>
                                        <tr>
                                            <th>Invoice No</th>
                                            <td>{{ $selldata->invoice_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Invoice Date</th>
                                            <td>{{ date('d/M/y', strtotime($selldata->date)) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table class="table-bordered table-striped table-condensed table">
                    <thead>
                        <tr style="background: rgba(36,36,37,0.27)">
                            <th class="service">S.NO.</th>
                            <th class="desc">PRODUCT</th>
                            <th>PRICE</th>
                            <th>QTY</th>
                            <th>TOTAL</th>
                        </tr>
                        <?php $subtotal = 0; ?>
                    </thead>
                    <tbody>
                        @foreach ($selldata->sellDetail as $key => $sellData)
                            @php
                                $sub = $sellData->productInfo->current_sale_price * $sellData->sale_quantity;
                            @endphp
                            <tr>
                                <td class="service">{{ $key + 1 }}</td>
                                <td class="desc">{{ $sellData->productInfo->name }}</td>
                                <td class="unit">
                                    {{ number_format($sellData->productInfo->current_sale_price, 0) }}
                                </td>
                                <td class="qty">{{ $sellData->sale_quantity }}</td>

                                <td class="total">
                                    {{ number_format($sellData->productInfo->current_sale_price * $sellData->sale_quantity, 0) }}
                                </td>
                                <?php $subtotal += $sub; ?>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="4">SUBTOTAL</td>
                            <td class="total">{{ number_format($subtotal, 0) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4">Discount</td>
                            <td class="total">{{ number_format($selldata->total_discount, 0) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="grand total">GRAND TOTAL</td>
                            <td class="grand total">{{ number_format($subtotal - $selldata->total_discount, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Return Remarks</label>
                <input type="text" name="return_remarks" id="return_remarks" class="form-control" value="-" />
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="d-flex justify-content-end p-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </form>
</div>
