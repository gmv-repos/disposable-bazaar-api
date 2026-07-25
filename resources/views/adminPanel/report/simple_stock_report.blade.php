@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h1>Simple Stock Report</h1>
                    <form action="{{ route('admin.report.simple-stock-report') }}" method="GET">
                        <div class="row justify-content-center align-items-center my-4">
                            <!-- Product Dropdown -->
                            <div class="col-sm-3">
                                <select name="product_id" class="form-control" id="product">
                                    <option value="">Select Product</option>
                                    @foreach ($allProducts as $product)
                                        <option value="{{ $product->id }}"
                                            {{ $request->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Range (optional for now) -->
                            <div class="col-sm-5 d-flex align-items-center gap-2">
                                <input type="date" name="from_date" value="{{ $request->from_date }}"
                                    class="form-control">
                                <span>to</span>
                                <input type="date" name="to_date" value="{{ $request->to_date }}" class="form-control">
                            </div>

                            <!-- Submit Button -->
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-info w-100">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr />
                    {{-- <div class="d-flex justify-content-end">
                        <form method="GET" action="{{ route('admin.report.simple-stock-report') }}" target="_blank">
                            <input type="hidden" name="product_id" value="{{ $request->product_id }}">
                            <input type="hidden" name="from_date" value="{{ $request->from_date }}">
                            <input type="hidden" name="to_date" value="{{ $request->to_date }}">
                            <input type="hidden" name="action" value="pdf">
                            <button type="submit" class="btn btn-secondary">
                                <i class="lni lni-printer"></i> PDF
                            </button>
                        </form>
                        {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-simple-stock-report') !!}
                    </div> --}}
                    <div id="printable-simple-stock-report">
                        <table id="example" class="table table-striped table-bordered dtReport">
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
