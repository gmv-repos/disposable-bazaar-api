<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
            }

            .container {
                width: 100%;
                background: #fff;
                /* border: 1px solid #ddd; */
                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            }

            .header {
                padding: 10px 20px;
                /* background: #f9f9f9; */
                justify-content: space-between;
                align-items: center;
            }

            /*
        .header h1 {
            color: #0b71f0;
            margin: 0;
            font-size: 24px;
        } */

            .header img {
                width: 80px;
                height: auto;
            }



            .table-container {
                width: 100%;
                margin-top: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }

            .table-container table th,
            .table-container table td {
                padding: 8px;
                font-size: 14px;
            }


            .items-table th,
            .items-table td {
                border: 1px solid #ddd;
                background-color: #f9f9f9;
                align-items: center;
                text-align: center;
            }

            .note {
                font-size: 13px;
                padding: 20px;
                color: #555;
            }

            .footer {
                text-align: center;
                background: #f9f9f9;
                font-size: 14px;
                padding: 10px;
                color: #333;
                font-weight: bold;
            }

            .bold {
                font-weight: bold;
            }

            .align-right {
                text-align: right;
            }

            .align-left {
                text-align: left;
            }

            .align-center {
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <!-- Header -->
            <div class="header">
                <table>
                    <tr>
                        <td>
                            <h1>
                                @if ($pdfIndex == 1)
                                    Invoice
                                @elseif ($pdfIndex == 2)
                                    Invoice
                                @elseif ($pdfIndex == 3)
                                    Delivery Challan
                                @elseif ($pdfIndex == 4)
                                    Gate Pass
                                @else
                                    Challan
                                @endif
                            </h1>
                        </td>
                        <td class="align-right">
                            <img src="{{ public_path('Frontend/Assets/Logo.png') }}" alt="Company Logo">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="table-container">
                <table>
                    <tr>
                        <td>
                            <b>Billing Address:</b> <br>
                            {{ $sell->customer->name }} <br>
                            {{ $sell->customer->phone }} <br>
                            {{ $sell->customer->address }} <br>
                        </td>
                        <td>
                            <b>Shipping Address:</b> <br>
                            {{ $sell->customer->name }} <br>
                            {{ $sell->customer->phone }} <br>
                            {{ $sell->customer->address }} <br>
                        </td>
                        <td>
                            <b>Invoice Details:</b> <br>
                            Invoice Date: {{ dateFormat(date('d-m-Y')) }} <br>
                            Invoice No: {{ $sell->invoice_id }} <br>
                            Sell Date: {{ dateFormat($sell->date) }} <br>
                            Payment Term: {{ dateFormat($sell->payment_date) }}
                        </td>
                    </tr>
                </table>
            </div>


            <!-- Table -->
            <div class="table-container items-table">
                <table>
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>PRODUCT</th>

                            {{-- Show per pc price only for Invoice --}}
                            @if (in_array($pdfIndex, [1, 2]))
                                <th>PRICE / PC</th>
                            @endif

                            <th>QTY (PCS)</th>

                            {{-- Show total price only for Invoice --}}
                            @if (in_array($pdfIndex, [1, 2]))
                                <th>TOTAL PRICE</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $subTotal = 0; @endphp

                        @foreach ($sell->sellDetail as $key => $item)
                            @php
                                $lineTotal = $item->unit_sell_price * $item->sale_quantity;
                                $subTotal += $lineTotal;
                            @endphp

                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="align-left">{{ $item->productInfo->name }}</td>

                                {{-- Per PC Price (Invoice only) --}}
                                @if (in_array($pdfIndex, [1, 2]))
                                    <td>{{ priceFormat($item->unit_sell_price) }}</td>
                                @endif

                                {{-- Total PCS only --}}
                                <td>{{ $item->sale_quantity }}</td>

                                {{-- Line Total (Invoice only) --}}
                                @if (in_array($pdfIndex, [1, 2]))
                                    <td>{{ priceFormat($lineTotal) }}</td>
                                @endif
                            </tr>
                        @endforeach


                        @if (in_array($pdfIndex, [1, 2]))
                            @if ($subTotal > 0)
                                <tr>
                                    <td colspan="3"></td>
                                    <td>Subtotal</td>
                                    <td>{{ priceFormat($subTotal) }}</td>
                                </tr>
                            @endif

                            @if ($sell->shipping_cost > 0)
                                <tr>
                                    <td colspan="3"></td>
                                    <td>Shipping</td>
                                    <td>{{ priceFormat($sell->shipping_cost) }}</td>
                                </tr>
                            @endif

                            @if ($sell->additional_charges > 0)
                                <tr>
                                    <td colspan="3"></td>
                                    <td>Additional Charges</td>
                                    <td>{{ priceFormat($sell->additional_charges) }}</td>
                                </tr>
                            @endif

                            @if ($sell->total_discount > 0)
                                <tr>
                                    <td colspan="3"></td>
                                    <td>Discount</td>
                                    <td>{{ priceFormat($sell->total_discount) }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="3"></td>
                                <td class="bold">Total</td>
                                <td class="bold">{{ priceFormat($sell->total_payable_amount) }}</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="note">
            One of our customer service representatives will call to confirm your order. Only those orders which have
            been verified through call will be dispatched.
        </div>

        <!-- Footer -->
        <div class="footer">
            Notify within 48 hours after delivery in any circumstances for return
        </div>
        </div>
    </body>

</html>







{{-- <div class="{{ $sell->order_status == 7 ? 'image' : '' }}">
    <div class="{{ $sell->order_status == 7 ? 'transparentbox' : '' }}">
        <header class="clearfix">
            <div style="background: #e9e9e9;padding: 10px 20px;margin-bottom: 20px">

                <div style="text-align: center;font-size: 30px">
                    <img src="{{ public_path('Frontend/Assets/Logo.png') }}" style="width:100px; height:50px;">
                </div>

            </div>
            <div id="company" style="font-size: 16px" class="clearfix">
                <div style="font-size: 30px;font-weight: 700">
                    <h1>
                        @if ($pdfIndex == 1)
                            Invoice 1
                        @elseif ($pdfIndex == 2)
                            Invoice 2
                        @else
                            Challan
                        @endif
                    </h1>
                </div>
                <div><span>Invoice#</span>: {{ $sell->invoice_id }}</div>
                <div class="subtextstyle"><span>Payment Date</span>: {{ dateFormat($sell->payment_date) }}</div>
            </div>
            <div id="project" style="margin-top:27px;font-size: 16px">
                <div> {{ $sell->customer->name }}</div>
                <div class="subtextstyle">{{ $sell->customer->address }}</div>
                <div class="subtextstyle">{{ $sell->customer->phone }}</div>
            </div>
        </header>
        <main>
            <table style="width:100%;">
                <thead>
                    <tr style="background: rgba(36,36,37,0.27)">
                        <th class="">S.NO.</th>
                        <th class="">PRODUCT</th>
                        <th>UNIT PRICE</th>
                        <th>QTY</th>
                        <th>TOTAL</th>
                    </tr>
                    <?php $subtotal = 0; ?>
                </thead>
                <tbody>
                    @foreach ($sell->sellDetail as $key => $sellData)
                        @php
                            $sub = ($sellData->unit_sell_price - $sellData->total_discount) * $sellData->sale_quantity;
                        @endphp
                        <tr>
                            <td class="">{{ $key + 1 }}</td>
                            <td class="">
                                {{ $sellData->productInfo->name }}

                            </td>
                            <td class="unit">
                                {{ number_format($sellData->unit_sell_price, 2) }}
                            </td>
                            <td class="qty">{{ $sellData->sale_quantity }}</td>

                            <td class="total">
                                {{ number_format($sellData->unit_sell_price * $sellData->sale_quantity - $sellData->total_discount * $sellData->sale_quantity, 2) }}
                            </td>
                            <?php $subtotal += $sub; ?>
                        </tr>
                    @endforeach

                    @
                    <tr>
                        <td colspan="4">SUBTOTAL</td>
                        <td class="total">{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Area Cost</td>
                        <td class="">{{ number_format($sell->shipping_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Additional Charges</td>
                        <td class="">{{ number_format($sell->additional_charges, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4">Discount</td>
                        <td class="total">{{ number_format($sell->total_discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="grand total">GRAND TOTAL</td>
                        <td class="grand total">{{ number_format($sell->total_payable_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </main>
        <footer>
            Invoice was created on a computer and is valid without the signature and seal.
        </footer>
    </div>
</div> --}}
