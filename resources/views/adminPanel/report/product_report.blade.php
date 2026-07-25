@extends('adminPanel.layout.layout')
@section('main_content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                
                <form action="">
                    <div class="row justify-content-center align-items-center my-4">
                        <div class="col-sm-3">
                            <select name="product" class="form-control" id="product">
                                <option value="">All</option>
                                @foreach ($allProducts as $product)
                                <option value="{{ $product->id }}" {{ request()->product == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-sm-3">
                            <input type="date" name="fromDate" value="{{ request()->fromDate }}" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <input type="date" name="toDate" value="{{ request()->toDate }}" class="form-control">
                    </div> --}}
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-info w-100">
                            Search
                        </button>
                    </div>
            </div>
            </form>
            <hr />
            <table id="example" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.NO.</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Total Variants</th>
                        <th>Current Sale Price</th>
                        <th>Available QTY</th>
                        <th class="text-center">E-Commerce</th>
                        <th class="text-center">POS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $key => $value)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->productCategory->name ?? 'N/A' }}</td>
                        <td>{{ $value->brand->name ?? 'N/A' }}</td>
                        <td>{{ $value->productVariants->count() }}</td>
                        <td>{{ $value->current_sale_price }}</td>
                        <td>{{ $value->available_quantity }}</td>
                        <td>
                            <div class="border p-2 bg-light">
                                <div class="w-100">
                                    <div class="w-50">Total Orders : </div>
                                    <div class="w-50">
                                        {{ $value->orderDetails->count() }}
                                    </div>
                                </div>
                                <hr />
                                <div class="w-100">
                                    <div class="w-50">Total QTY : </div>
                                    <div class="w-50">
                                        {{ $value->orderDetails->sum('qty') }}
                                    </div>
                                </div>
                                <hr />
                                <div class="w-100">
                                    <div class="w-50">Total Amount : </div>
                                    <div class="w-50">
                                        {{ $value->orderDetails->sum('product_sub_total') }}
                                    </div>
                                </div>
                        </td>
                        <td>
                            <div class="border p-2 bg-light">
                                <div class="w-100">
                                    <div class="w-50">Total Sells : </div>
                                    <div class="w-50">
                                        {{ $value->sellDetails->count() }}
                                    </div>
                                </div>
                                <hr />
                                <div class="w-100">
                                    <div class="w-50">Total QTY : </div>
                                    <div class="w-50">
                                        {{ $value->sellDetails->sum('sale_quantity') }}
                                    </div>
                                </div>
                                <hr />
                                <div class="w-100">
                                    <div class="w-50">Total Discount : </div>
                                    <div class="w-50">
                                        {{ $value->sellDetails->sum('total_discount') }}
                                    </div>
                                </div>
                                <hr />
                                <div class="w-100">
                                    <div class="w-50">Total Amount : </div>
                                    <div class="w-50">
                                        {{ $value->sellDetails->sum('total_payable_amount') }}
                                    </div>
                                </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
<!--end page wrapper -->
@endsection
@section('css_plugins')
<link href="{{asset('assets/adminPanel')}}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js_plugins')

<script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')
<script>
    $(document).ready(function() {

        $('#product').select2();
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