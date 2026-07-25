<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Simple Stock Report</title>
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
        <h2>Simple Stock Report</h2>
        <div>Date: {{ now()->format('d M Y') }}</div>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>Product Name</th>
                    <th>Stock</th>
                    <th>AVG Purchase Price</th>
                    <th>AVG Sell Price</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach ($reportData as $dRow)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $dRow->product_name }}</td>
                        <td>{{ number_format($dRow->stock, 2) }}</td>
                        <td>{{ number_format($dRow->avg_purchase_price, 2) }}</td>
                        <td>{{ number_format($dRow->avg_sell_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>            
        </table>
    </main>

</body>

</html>