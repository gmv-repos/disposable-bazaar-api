@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <h1>Order Report</h1>
                    <form action="{{ route('admin.report.order') }}" method="GET">
                        <div class="row justify-content-center align-items-center my-4">
                            <div class="col-sm-2">
                                <select name="product" class="form-control" id="product">
                                    <option value="">Select Product</option>
                                    @foreach ($allProducts as $product)
                                        <option value="{{ $product->id }}"
                                            {{ request()->product == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
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
                    <div class="d-flex justify-content-end mb-2">
                        <form method="GET" action="{{ route('admin.report.order') }}" target="_blank">
                            <input type="hidden" name="action" value="pdf">
                            <input type="hidden" name="product" value="{{ $filterData['product'] }}">
                            <input type="hidden" name="startdate" value="{{ $filterData['startdate'] }}">
                            <input type="hidden" name="enddate" value="{{ $filterData['enddate'] }}">
                            <button type="submit" class="btn btn-secondary">
                                <i class="lni lni-printer"></i> PDF
                            </button>
                        </form>
                        {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-order-report') !!}
                    </div>
                    <div id="printable-order-report">
                        <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.NO.</th>
                                    <th>Product Name</th>
                                    <th>Product Code</th>
                                    <th>Quantity Solds</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach ($allOrderDetails as $summary)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $summary['product_name'] }}</td>
                                        <td>{{ $summary['product_code'] }}</td>
                                        <td>{{ $summary['total_qty'] }}</td>
                                        <td>{{ $summary['unit_price'] }}</td>
                                        <td>{{ $summary['total_price'] }}</td>
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

            var table = $('#example').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
