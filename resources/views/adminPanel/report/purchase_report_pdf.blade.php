<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Purchase Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0 auto;
            font-size: 12px;
        }

        header {
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        #logo img {
            width: 100px;
            height: auto;
        }

        h2 {
            margin-top: 10px;
            color: #5D6975;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: bold;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            font-size: 11px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            color: #888;
        }
    </style>
</head>

<body>

    <header>
        <div id="logo">
            <img src="{{ public_path('Frontend/Assets/Logo.png') }}" alt="Company Logo">
        </div>
        <h2>Purchase Report</h2>
        <div>Date: {{ now()->format('d M Y') }}</div>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>Date of Purchase</th>
                    <th>Purcahse Number</th>
                    <th>Supplier</th>
                    <th>Product Details</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                @php $index = 1; @endphp
                @foreach ($allpurchase as $index => $purchase)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $purchase->created_at->format('d M Y') }}</td>
                        <td>{{ $purchase->pr_code }}</td>
                        <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                        <td>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Cost</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->prItems as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->total_qty }}</td>
                                            <td>{{ $item->cost_amount }}</td>
                                            <td>{{ $item->total_cost_amount }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                        <td>{{ $purchase->payable_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>

</html>
