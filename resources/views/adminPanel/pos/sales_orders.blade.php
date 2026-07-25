@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Sales Orders</h5>
                <div class="card-action">
                    <a href="{{ route('admin.pos.createSalesOrder') }}" class="btn btn-primary btn-sm">Create Sales
                        Order</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>S.NO.</th>
                                <th>Date</th>
                                <th>SO No#</th>
                                <th>Order Items Details</th>
                                <th>Total Cost</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesOrders as $salesOrder)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $salesOrder->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        {{ $salesOrder->sales_order_number }}

                                    </td>
                                    <td>
                                        <table class="w-100 table table-sm table-bordered bg-white">
                                            <thead>
                                                <tr class="bg-white text-dark">
                                                    <th>Item</th>
                                                    <th>Brand</th>
                                                    <th>Pack Size / Custom</th>
                                                    <th>Price</th>
                                                    <th>Order QTY</th>
                                                    <th>Delivered QTY</th>
                                                    <th>Remaining QTY</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($salesOrder->items as $salesOrderItem)
                                                    <tr class="bg-white text-dark">
                                                        <td>{{ $salesOrderItem->product->name }}</td>
                                                        <td>{{ $salesOrderItem->brand->name ?? "N/A" }}</td>
                                                        <td>{{ $salesOrderItem->itemVariant->variant->pack_size ?? "custom" }}</td>
                                                        <td>{{ number_format($salesOrderItem->sell_price, 2) }}</td>
                                                        <td>{{ $salesOrderItem->quantity }}</td>
                                                        <td>{{ $salesOrderItem->delivered_quantity }}</td>
                                                        <td>
                                                            {{ $salesOrderItem->quantity - $salesOrderItem->delivered_quantity }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        </table>
                                    </td>
                                    <td>{{ number_format($salesOrder->total_cost, 2) }}</td>
                                    <td>{{ number_format($salesOrder->balance, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $salesOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css_plugins')
    <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js_plugins')
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection