@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h1>Purchase Report</h1>
                    <form action="{{ route('admin.report.purchase') }}" method="GET">
                        <div class="row justify-content-center align-items-center my-4">
                            <div class="col-sm-2">
                                <select name="supplier" class="form-control" id="supplier">
                                    <option value="">Select Supplier</option>
                                    @foreach ($allSuppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ request()->supplier == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4 d-flex">
                                <input type="date" name="startdate" value="{{ request()->startdate }}"
                                    class="form-control">
                                &nbsp;
                                <span style="margin-top: 5px;">To</span>
                                &nbsp;
                                <input type="date" name="enddate" value="{{ request()->enddate }}" class="form-control">
                                &nbsp;
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-info w-100">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <hr />
                    {{-- <div class="d-flex justify-content-end mb-2">
                        <form method="GET" action="{{ route('admin.report.purchase') }}" target="_blank">
                            <input type="hidden" name="action" value="pdf">
                            <input type="hidden" name="supplier" value="{{ $filterData['supplier'] }}">
                            <input type="hidden" name="startdate" value="{{ $filterData['startdate'] }}">
                            <input type="hidden" name="enddate" value="{{ $filterData['enddate'] }}">
                            <button type="submit" class="btn btn-secondary">
                                <i class="lni lni-printer"></i> PDF
                            </button>
                        </form>
                        {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-purchase-report') !!}
                    </div> --}}
                    <div id="printable-purchase-report">

                        <table id="example" class="table table-striped table-bordered dtReport">
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
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--end page wrapper -->
@endsection
@section('css_plugins')
    <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js_plugins')
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')
    <script>
        $(document).ready(function() {

            $('#product').select2();
            $('#supplier').select2();
            // $('#example').DataTable({});

            // var table = $('#example').DataTable({
            //     lengthChange: false,
            //     buttons: ['copy', 'excel', 'pdf', 'print']
            // });

            // table.buttons().container()
            //     .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
