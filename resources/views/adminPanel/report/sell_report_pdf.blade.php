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

            </tr>
            </thead>
            <tbody>
            @foreach($sellProduct as $key=>$sellInfo)
            <tr>
                <td>{{$key+1}}</td>
                <td>#{{$sellInfo->code}}</td>
                <td>{{$sellInfo->name}}</td>
                <td>{{$sellInfo->total_sell}}</td>

            </tr>
            @endforeach

            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </main>

</body>

</html>
