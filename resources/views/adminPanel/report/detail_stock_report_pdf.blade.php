<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Detail Stock Report</title>
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
        <h2>Detail Stock Report</h2>
        <div>Date: {{ now()->format('d M Y') }}</div>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Brand = QTY</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData as $dRow)
                    <tr>
                        <td>{{ $dRow->product_name }}</td>
                        <td>
                            <table class="border w-100">
                                @foreach ($dRow->brandWise as $bw)
                                    <tr>
                                        <td class="border text-center">
                                            {{ $bw->name }} = {{ $bw->qty }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td>{{ $dRow->total_qty }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </main>

</body>

</html>