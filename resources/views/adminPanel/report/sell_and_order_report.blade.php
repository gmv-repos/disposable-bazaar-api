@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Product Sell Report</h4>
                <form action="{{ route('admin.report.sellAndOrderReport') }}" method="GET">
                    <div class="d-flex gap-3 justify-content-center align-items-center mt-4">
                        <input type="date" name="fromDate" value="{{ request()->fromDate }}" class="form-control form-control-sm">
                        <input type="date" name="toDate" value="{{ request()->toDate }}" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-info btn-sm w-100">Search</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="printable-purchase-report">

                        <table class="table table-striped table-bordered dtReport">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sale QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportData as $rRow)
                                    <tr>
                                        <td>{{ $rRow->product_name }}</td>
                                        <td>{{ $rRow->total_qty }}</td>
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