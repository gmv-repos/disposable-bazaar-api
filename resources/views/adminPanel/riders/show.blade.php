@extends('adminPanel.layout.layout')


@section('main_content')
<div class="page-content">

    <!-- Add filter dropdown -->
    <div class="card">
        <div class="card-header">
            <h4>
                Rider's Order
            </h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('riders.show', $rider->id) }}">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">All Orders</option>
                            <option value="1" {{ ixiSelected(request('status'), 1) }}>Pending</option>
                            <option value="2" {{ ixiSelected(request('status'), 2) }}>Processing</option>
                            <option value="3" {{ ixiSelected(request('status'), 3) }}>On The Way</option>
                            <option value="4" {{ ixiSelected(request('status'), 4) }}>Canceled</option>
                            <option value="5" {{ ixiSelected(request('status'), 5) }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="payStatus">
                            <option value="">All</option>
                            <option value="paid" {{ request('payStatus') == 'paid' ? 'selected' : '' }}>
                                Paid
                            </option>
                            <option value="unpaid" {{ request('payStatus') == 'unpaid' ? 'selected' : '' }}>
                                Unpaid
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="payMethod" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="1" {{ ixiSelected(request('payMethod'), 1) }}>
                                COD
                            </option>
                            <option value="2" {{ ixiSelected(request('payMethod'), 2) }}>
                                Online
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="from_date" name="from_date" class="form-control" value="{{ old('from_date', $fromDate) }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="to_date" name="to_date" class="form-control" value="{{ old('to_date', $toDate) }}">
                    </div>

                    <div class="col-md-2 d-flex">
                        <button type="button" class="btn btn-success w-50" onclick="this.form.submit()">
                            Filter
                        </button>
                        <a href="{{ route('riders.show', $rider->id) }}" class="ms-2 btn btn-info w-50">
                            Clear
                        </a>
                    </div>
                </div>
            </form>

            <hr class="mb-4" />

            <!-- Display filtered orders -->

            <div class="table-responsive">
                <form method="get" action="" id="ordersTableForm">
                    <input type="hidden" name="status" id="inputOrdersStatus">
                    <input type="hidden" name="pay_status" id="inputOrdersPayStatus">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th>S.NO.</th>
                                <th>Order No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Price</th>
                                <th>Delivery Charges</th>
                                <th>Total</th>
                                <th>Rider</th>
                                <th>Order Status</th>
                                <th>Pay Method</th>
                                <th>Pay Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderList as $key => $order)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}">
                                </td>
                                <td>{{ $key+1 }}</td>
                                <td># {{ $order->order_no }}</td>
                                <td>
                                    {{ is_null($order->customer_id)
                                        ? 'Guest'
                                        : $order->customer->name }}
                                </td>
                                <td>{{ $order->customer->phone ?? '' }}</td>
                                <td>{{ round($order->total_amount) }}</td>
                                <td>{{ number_format($order->shipping_charges, 2, '.', ',') }}</td>
                                <td>{{ round($order->grand_total) }}</td>

                                <td>
                                    @if (!is_null($order->rider_id))
                                    <a href="{{ route('riders.show', $order->rider_id) }}">
                                        {{ $order->rider->name }}
                                    </a>
                                    @else
                                    <span>N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->order_status == 1)
                                    <span class="badge bg-success">Pending</span>
                                    @elseif($order->order_status == 2)
                                    <span class="badge bg-warning">Processing</span>
                                    @elseif($order->order_status == 3)
                                    <span class="badge bg-primary">On The Way</span>
                                    @elseif($order->order_status == 4)
                                    <span class="badge bg-danger">Canceled</span>
                                    @elseif($order->order_status == 5)
                                    <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($order->pay_method == 1)
                                    <span class="badge bg-info px-3">Cash on Delivery</span>
                                    @elseif($order->pay_method == 2)
                                    <span class="badge bg-success px-3">Online</span>
                                    @elseif($order->pay_method == 3)
                                    <span class="badge bg-primary px-3">Online Without Delivery Charges</span>
                                    @else
                                    <span class="badge bg-light px-3">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($order->rider_pay_status == 'paid')
                                    <span class="badge bg-success px-3">
                                        Paid to {{ $order->pay_method == 1 ? 'company' : 'rider' }}
                                    </span>
                                    @elseif($order->rider_pay_status == 'unpaid')
                                    <span class="badge bg-warning px-3">
                                        Unpaid to {{ $order->pay_method == 2 ? 'rider' : 'company' }}
                                    </span>
                                    @else
                                    <span class="badge bg-light px-3">x</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">Settings
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.order.detail', ['order_id' => $order->id]) }}">Order Detail</a></li>

                                            @if($order->order_status != 1 && $order->order_status != 5)
                                            <li><a class="dropdown-item" href="{{ route('admin.order.status.update', ['status' => 1, 'order_ids[]' => $order->id]) }}">Pending</a></li>
                                            @endif
                                            @if($order->order_status != 2 && $order->order_status != 5)
                                            <li><a class="dropdown-item" href="{{ route('admin.order.status.update', ['status' => 2, 'order_ids[]' => $order->id]) }}">Processing</a></li>
                                            @endif
                                            @if($order->order_status != 3 && $order->order_status != 5)
                                            <li><a class="dropdown-item" href="{{ route('admin.order.status.update', ['status' => 3, 'order_ids[]' => $order->id]) }}">On The Way</a></li>
                                            @endif
                                            @if($order->order_status != 4 && $order->order_status != 5)
                                            <li><a class="dropdown-item" href="{{ route('admin.order.status.update', ['status' => 4, 'order_ids[]' => $order->id]) }}">Cancel Order</a></li>
                                            @endif
                                            @if($order->order_status != 5)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.order.status.update', ['status' => 5, 'order_ids[]' => $order->id]) }}">
                                                    Complete
                                                </a>
                                            </li>
                                            @endif

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-4 d-flex">
                    <select class="form-control" name="status" id="selectOrdersStatus">
                        <option value="1">Pending</option>
                        <option value="2">Processing</option>
                        <option value="3">On The Way</option>
                        <option value="4">Canceled</option>
                        <option value="5">Completed</option>
                    </select>
                    <button type="button" class="btn btn-success ms-2" onclick="submitOrdersStatusUpdateForm()">
                        Change
                    </button>
                </div>

                @if (request('payStatus') && request('payStatus') != '')
                <div class="col-md-4 d-flex">
                    <select class="form-control" name="pay_status" id="selectOrdersPayStatus">
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                    <button type="button" class="btn btn-success ms-2" onclick="changeOrdersPayStatus()">
                        Change
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Rider Details</h5>
        </div>
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Pay to Rider</h6>
                            <hr />
                            <h5>{{ $payToRider }}</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Pay to Company</h6>
                            <hr />
                            <h5>{{ $payToCompany }}</h5>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Online Without Delivery Charges</h6>
                            <hr />
                            <h5>{{ $onlineWithoutDC }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mb-4" />
            <div class="row">
                <!-- Rider Name -->
                <div class="col-md-4 mb-3">
                    <h6>Rider Name</h6>
                    <p>{{ $rider->name }}</p>
                </div>

                <!-- Phone -->
                <div class="col-md-4 mb-3">
                    <h6>Phone</h6>
                    <p>{{ $rider->phone }}</p>
                </div>

                <!-- Email -->
                <div class="col-md-4 mb-3">
                    <h6>Email</h6>
                    <p>{{ $rider->email ?? 'N/A' }}</p>
                </div>

                <!-- Address -->
                <div class="col-md-4 mb-3">
                    <h6>Address</h6>
                    <p>{{ $rider->address }}</p>
                </div>

                <!-- Status -->
                <div class="col-md-4 mb-3">
                    <h6>Status</h6>
                    <p>
                        @if($rider->status === 'active')
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </p>
                </div>

                <!-- Created At -->
                <div class="col-md-4 mb-3">
                    <h6>Created At</h6>
                    <p>{{ \Carbon\Carbon::parse($rider->created_at)->format('d M, Y h:i A') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('riders.index') }}" class="btn btn-secondary btn-sm">
                    <i class="lni lni-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('riders.edit', $rider->id) }}" class="btn btn-primary btn-sm">
                    <i class="lni lni-pencil"></i> Edit
                </a>
                <form action="{{ route('riders.destroy', $rider->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                        <i class="lni lni-trash"></i> Delete
                    </button>
                </form>
            </div>

        </div>
    </div>


</div>


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
        var table = $('#example').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'print']
        });


        $('#select_all').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[name="order_ids[]"]').prop('checked', isChecked);
        });

    });

    function submitOrdersStatusUpdateForm() {

        let selectedOrdersCount = $('input[name="order_ids[]"]:checked').length;
        if(selectedOrdersCount == 0){
            warningToast('Please select at least one order');
            return false;
        }

        $('#ordersTableForm').attr('action', "/");

        var selectOrdersStatus = $('#selectOrdersStatus').val();
        $('#inputOrdersStatus').val(selectOrdersStatus);
        $('#ordersTableForm').attr('action', "{{ route('admin.order.status.update') }}");
        $('#ordersTableForm').submit();
    }


    function changeOrdersPayStatus() {

        let selectedOrdersCount = $('input[name="order_ids[]"]:checked').length;
        if(selectedOrdersCount == 0){
            warningToast('Please select at least one order');
            return false;
        }
        
        $('#ordersTableForm').attr('action', "/");

        var selectOrdersPayStatus = $('#selectOrdersPayStatus').val();
        $('#inputOrdersPayStatus').val(selectOrdersPayStatus);
        $('#ordersTableForm').attr('action', "{{ route('admin.order.pay.status.update') }}");

        $('#ordersTableForm').submit();
    }
</script>
@endsection

@endsection