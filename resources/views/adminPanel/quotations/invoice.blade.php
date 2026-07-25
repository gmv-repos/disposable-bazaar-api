<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quotation</title>
    {{-- <link rel="stylesheet" href="style.css" media="all" /> --}}


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap');
    </style>

    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            position: relative;
            /*width: 21cm;*/
            height: 29.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: 'Work Sans', sans-serif;
        }

        .subtextstyle {
            font-size: 13px;
            color: #333232;
            margin-top: 2px;
        }

        header {
            padding: 0px 0;
            /*margin-bottom: 30px;*/
        }

        #logo {
            text-align: left;
            /*margin-bottom: 10px;*/
        }

        #logo img {
            width: 100px;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 10px 0;
            background: url(dimension.png);
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 20px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }

        div.image {
            background: url({{ asset('assets/adminPanel/images/returned.png') }});
            background-repeat: no-repeat;
            background-position: center;
        }

        div.transparentbox {
            background-color: #ffffff;
            opacity: 0.9;
            filter: alpha(opacity=60);
        }

        div.transparentbox p {
            font-weight: bold;
            color: #CD853F;
        }
    </style>

</head>

<body>
    <div class="">
        <div class="transparentbox">
            <header class="clearfix">
                <div style="background: #e9e9e9;padding: 10px 20px;margin-bottom: 20px">

                    <div style="text-align: center;font-size: 30px">
                        <img src="{{ public_path('Frontend/Assets/Logo.png') }}" style="width:100px; height:50px;">
                    </div>

                </div>
                <div id="company" style="font-size: 16px" class="clearfix">
                    <div style="font-size: 30px;font-weight: 700">Quotation</div>
                    <div><span>Quotation#</span>: {{ $quotation->reference_code }}</div>
                    <div class="subtextstyle"><span>Date</span>: {{ date('d/M/y', strtotime($quotation->created_at)) }}
                    </div>
                </div>
                <div id="project" style="margin-top:27px;font-size: 16px">
                    <div> Customer : {{ $quotation->customer_name ?? 'N/A' }}</div>
                    <div> Company : {{ $quotation->company_name ?? 'N/A' }}</div>
                </div>
            </header>
            <main>
                <table class="table w-100" id="QuotationItemsTable">
                    <thead>
                        <tr class="bg-light text-dark rounded">
                            <th>Img</th>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Variants</th>
                            <th>Price Per Piece</th>
                            <th>QTY</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($quotation->quotationItems as $qItem)
                            <tr class="bg-light text-dark rounded" productID="{{ $qItem->product_id }}">
                                <td>
                                    @if ($qItem->product->image_path)
                                        <img src="{{ asset($qItem->product->image_path) }}" alt="Product Image"
                                            class="img-fluid" width="50px">
                                    @else
                                        <img src="{{ asset('assets/images/default.png') }}" alt="Product Image"
                                            class="img-fluid" width="50px">
                                    @endif
                                </td>
                                <td>{{ $qItem->product->name }}</td>
                                <td>{{ $qItem->brand->name ?? 'N/A' }}</td>
                                <td>{{ $qItem->productVariant->variant->pack_size ?? 'N/A' }}</td>
                                <td class="itemPrice">{{ $qItem->price }}</td>
                                <td>
                                    {{ $qItem->quantity }}
                                </td>

                                <td class="itemTotalPrice">{{ $qItem->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>

                        <tr>
                            <th colspan="5"></th>
                            <th>Grand Total</th>
                            <th id="grandTotal">
                                {{ $quotation->quotationItems->sum('total') }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5"></th>
                            <th>Discount</th>
                            <th>
                                {{ $quotation->discount }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5"></th>
                            <th>TAX</th>
                            <th>
                                {{ $quotation->tax }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="5"></th>
                            <th>Payable Amount</th>
                            <th>{{ $quotation->payable_amount }}</th>
                        </tr>
                    </tfoot>
                </table>
            </main>
            <footer>
                Quotation was created on a computer and is valid without the signature and seal.
            </footer>
        </div>
    </div>
</body>

</html>
