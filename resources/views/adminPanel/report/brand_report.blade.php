@extends('adminPanel.layout.layout')
@section('main_content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <form action="">
                    <div class="row justify-content-center align-items-center my-4">
                        <div class="col-sm-3">
                            <select name="brand" class="form-control" id="brand">
                                <option value="">All</option>
                                @foreach ($allBrands as $brand)
                                <option value="{{ $brand->id }}" {{ request()->brand == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
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
            <table id="example" class="table table-striped table-bordered dtReport">
                <thead>
                    <tr>
                        <th>S.NO.</th>
                        <th>Name</th>
                        <th>Total Products</th>
                        <th class="text-center">Tranding Product (E-Commerce)</th>
                        <th class="text-center">Tranding Product (POS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $key => $value)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->products->count() }}</td>
                        @php
                        $mostOrderedProduct = $value->products()->withCount('orderDetails as totalOrders')->orderBy('totalOrders', 'desc')->first()
                        @endphp
                        <td>
                            <div class="border p-2 bg-light">
                                <div class="w-100">
                                    {{ $mostOrderedProduct->name ?? "N/A" }}
                                </div>
                                <hr />
                                <div class="w-100 d-flex">
                                    <div class="me-2">Orders : </div>
                                    <div class="">
                                        {{ $mostOrderedProduct->totalOrders ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        @php
                        $mostSoldProduct = $value->products()->withCount('sellDetails as totalSells')->orderBy('totalSells', 'desc')->first()
                        @endphp
                        <td>
                            <div class="border p-2 bg-light">
                                <div class="w-100">
                                    {{ $mostSoldProduct->name ?? "N/A" }}
                                </div>
                                <hr />
                                <div class="w-100 d-flex">
                                    <div class="me-2">Sells : </div>
                                    <div class="">
                                        {{ $mostSoldProduct->totalSells ?? 0 }}
                                    </div>
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

        $('#brand').select2();
        // $('#example').DataTable({});

        // var table = $('#example').DataTable({
        //     lengthChange: false,
        //     buttons: ['copy', 'excel', 'pdf', 'print']
        // });

        // table.buttons().container()
        //     .appendTo('#example_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection