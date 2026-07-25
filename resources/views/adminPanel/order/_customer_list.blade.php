@extends('adminPanel.layout.layout')

@section('main_content')
<!--start page wrapper -->
<div class="page-content">

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ecommerce.customer.list') }}">
                <div class="row align-items-center justify-content-center">                    
                    <div class="col-md-4">
                        <input type="date" id="from_date" name="from_date" class="form-control" value="{{ old('from_date', $from_date) }}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" id="to_date" name="to_date" class="form-control" value="{{ old('to_date', $to_date) }}">
                    </div>

                    <div class="col-md-2 d-flex">
                        <button type="button" class="btn btn-success" onclick="this.form.submit()">
                            Filter
                        </button>
                        <a href="{{ route('admin.ecommerce.customer.list') }}" class="ms-2 btn btn-info">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="customerTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Orders Count</th>
                            <th>Total Amount Spent</th>
                            <th>Total Paid</th>
                            <th>Due Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customerData as $key => $data)
                        <tr>
                            <td>{{ (is_numeric($key) ? (int)$key : 0) + 1 }}</td>
                            <td>{{ $data['name'] }}</td>
                            <td>{{ $data['phone'] }}</td>
                            <td>{{ $data['email'] }}</td>
                            <td>{{ $data['orders_count'] }}</td>
                            <td>{{ round($data['total_spent'], 2) }}</td> <!-- Total spent -->
                            <td>{{ round($data['total_paid'], 2) }}</td> <!-- Total paid -->
                            <td>{{ round($data['due_amount'], 2) }}</td> <!-- Due amount -->
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
<link href="{{ asset('assets/adminPanel/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('js_plugins')
<script src="{{ asset('assets/adminPanel/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/adminPanel/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#customerTable').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'print']
        });

        table.buttons().container()
            .appendTo('#customerTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection