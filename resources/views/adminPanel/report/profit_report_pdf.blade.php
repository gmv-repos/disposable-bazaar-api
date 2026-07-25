<?php
$totalCost = 0;
$totalSell = 0;
$totalProfit = 0;
?>
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
        <h2>Sell Profit</h2>
        <div>Date: {{ now()->format('d M Y') }}</div>
    </header>

    <main>
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Sell Quantity</th>
                    <th>Total Cost</th>
                    <th>Total Sell Price</th>
                    <th>Total Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sellProduct as $key => $sellInfo)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>#{{ $sellInfo->code }}</td>
                        <td>{{ $sellInfo->name }}</td>
                        <td class="tb_tx_right">{{ $sellInfo->total_sell }}</td>
                        <td class="tb_tx_right">{{ $sellInfo->total_cost }}</td>
                        <td class="tb_tx_right">{{ $sellInfo->total_sell_price }}</td>
                        <td class="tb_tx_right">{{ $sellInfo->total_sell_price - $sellInfo->total_cost }}</td>
                    </tr>
                    <?php
                    $totalCost += $sellInfo->total_cost;
                    $totalSell += $sellInfo->total_sell_price;
                    $totalProfit += $sellInfo->total_sell_price - $sellInfo->total_cost;
                    ?>
                @endforeach

            </tbody>
            <tfoot class="footer_div">
                <td colspan="3"></td>
                <td colspan="">TOTAL</td>
                <td colspan="">{{ $totalCost }}</td>
                <td colspan="">{{ $totalSell }}</td>
                <td colspan="">{{ $totalProfit }}</td>
            </tfoot>
        </table>
    </main>

</body>

</html>
