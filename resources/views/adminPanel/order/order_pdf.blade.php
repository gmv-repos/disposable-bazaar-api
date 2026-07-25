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

            .header h1 {
                color: #0b71f0;
                margin: 0;
                font-size: 24px;
            }

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
                                @else
                                    Delivery Challan
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
                            {{ $order->name }} <br>
                            {{ $order->phone }} <br>
                            {{ $order->orderBilling->address }}<br>
                        </td>
                        <td>
                            <b>Shipping Address:</b> <br>
                            {{ $order->name }} <br>
                            {{ $order->phone }} <br>
                            {{ $order->orderBilling->address }}<br>
                        </td>
                        <td>
                            <b>Invoice Details:</b> <br>
                            Invoice Date: {{ dateFormat(date('d-m-Y')) }}<br>
                            Invoice No: {{ $order->order_no }}<br>
                            Order Date: {{ dateFormat($order->order_date) }}
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

                            {{-- Invoice only --}}
                            @if (in_array($pdfIndex, [1, 2]))
                                <th>PRICE / PC</th>
                            @endif

                            <th>QTY (PCS)</th>

                            {{-- Invoice only --}}
                            @if (in_array($pdfIndex, [1, 2]))
                                <th>TOTAL PRICE</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @php $subTotal = 0; @endphp

                        @foreach ($order->orderDetails as $key => $item)
                            @php
                                $lineTotal = $item->price * $item->qty;
                                $subTotal += $lineTotal;
                            @endphp

                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="align-left">{{ $item->product->name }}</td>

                                {{-- Per PC price (Invoice only) --}}
                                @if (in_array($pdfIndex, [1, 2]))
                                    <td>{{ priceFormat($item->price) }}</td>
                                @endif

                                {{-- Total PCS --}}
                                <td>{{ $item->qty }}</td>

                                {{-- Line total (Invoice only) --}}
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

                            @if ($order->shipping_charges > 0)
                                <tr>
                                    <td colspan="3"></td>
                                    <td>Shipping</td>
                                    <td>{{ priceFormat($order->shipping_charges) }}</td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="3"></td>
                                <td class="bold">Total</td>
                                <td class="bold">{{ priceFormat($order->grand_total) }}</td>
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
