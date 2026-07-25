@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">

        <!-- Add filter dropdown -->
        
        <!-- Display filtered orders -->
        <div class="card">

            <div class="card-body">
                <div class="row">
                    <h6 class="h6">Filters</h6>
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.ecommerce.order.list') }}">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" name="status">
                                        <option value="">All Orders</option>
                                        <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="2" {{ request('status') == 2 ? 'selected' : '' }}>Processing
                                        </option>
                                        <option value="3" {{ request('status') == 3 ? 'selected' : '' }}>On The Way
                                        </option>
                                        <option value="4" {{ request('status') == 4 ? 'selected' : '' }}>Canceled
                                        </option>
                                        <option value="5" {{ request('status') == 5 ? 'selected' : '' }}>Completed
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" name="byRider" id="byRider">
                                        <option value="">All</option>
                                        @foreach ($riders as $rider)
                                            <option value="{{ $rider->id }}"
                                                {{ ixiSelected($rider->id, request('byRider')) }}>
                                                {{ $rider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" value="{{ old('from_date', $from_date) }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        value="{{ old('to_date', $to_date) }}">
                                </div>

                                <div class="col-md-2 d-flex align-items-center justify-content-between">
                                    <button type="button" class="btn btn-success btn-sm w-50" onclick="this.form.submit()">
                                        Filter
                                    </button>
                                    <a href="{{ route('admin.ecommerce.order.list') }}"
                                        class="ms-2 btn btn-info btn-sm w-50">
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr class="my-3 fw-bold" />
                </div>
                <form method="POST" action="{{ route('admin.order.ordersMultiAction') }}" id="ordersMultiActionForm">
                    @csrf
                    <input type="hidden" name="ordersAction" id="ordersAction">
                    <div class="row align-items-center justify-content-between mb-3" id="ordersActionButtonsRow"
                        style="display: none">
                        <div class="col-md-4 d-flex align-items-center justify-content-between">
                            <select class="form-select form-select-sm" name="ordersStatus">
                                <option value="1">Pending</option>
                                <option value="2">Processing</option>
                                <option value="3">On The Way</option>
                                <option value="4">Canceled</option>
                                <option value="5">Completed</option>
                            </select>
                            <button type="button" class="btn btn-success btn-sm w-50 ms-1"
                                onclick="submitOrdersMultiActionForm('changeOrderStatus')">
                                Change
                            </button>
                        </div>
                        <div class="col-md-5 d-flex gap-3">
                            <select class="form-select form-select-sm w-50" id="riderID" name="riderID">
                                <option value="" selected>Rider</option>
                                @foreach ($riders as $rider)
                                    <option value="{{ $rider->id }}">
                                        {{ $rider->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select class="form-select form-select-sm w-25" id="paymentMethod" name="paymentMethod">
                                <option value="" selected>Pay Method</option>
                                <option value="1">COD</option>
                                <option value="2">Online</option>
                            </select>

                            <button type="button" class="btn btn-success btn-sm w-25"
                                onclick="submitOrdersMultiActionForm('allocateRiderAndPayMethod')">
                                Allocate
                            </button>
                        </div>
                        <div class="col-md-3 gap-2 d-flex">
                            <button type="button" class="btn btn-info btn-sm w-50"
                                onclick="submitOrdersMultiActionForm('print')">
                                Print
                            </button>
                            <button class="btn btn-danger btn-sm w-50" onclick="submitOrdersMultiActionForm('downloadPDF')">
                                PDF Download
                            </button>
                        </div>
                        <hr class="my-3 fw-bold" />
                    </div>

                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <!-- <input type="checkbox" id="select_all"> -->
                                    </th>
                                    <th>S.NO.</th>
                                    <th>Date</th>
                                    <th>Order No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Price</th>
                                    <th>Delivery Charges</th>
                                    <th>Total</th>
                                    <th>Rider</th>
                                    <th>Pay Method</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderList as $key => $order)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                                onchange="toggleOrdersActionButtonsRow()">
                                        </td>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ dateFormat($order->created_at) }}</td>
                                        <td># {{ $order->order_no }}</td>
                                        <td>
                                            {{ is_null($order->customer_id) ? 'Guest' : $order->customer->name }}
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

                                            @if ($order->pay_method == 1)
                                                <span class="badge bg-info">Cash On Delivery</span>
                                            @elseif($order->pay_method == 2)
                                                <span class="badge bg-success">Online Payment</span>
                                            @elseif($order->pay_method == 3)
                                                <span class="badge bg-primary">Online Payment Without DC</span>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {!! getOrderStatusBadge($order->order_status) !!}
                                        </td>
                                        <td>
                                            <div class="dropdown d-flex justify-content-center">
                                                <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                                </button>
                                                <ul class="dropdown-menu">

                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.order.detail', ['order_id' => $order->id]) }}">Order
                                                            Detail</a></li>
                                                    <!-- Only show status change options for orders that are not already completed (status != 5) -->
                                                    @if ($order->order_status != 1 && $order->order_status != 5)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.order.status.update', ['status' => 1, 'order_ids[]' => $order->id]) }}">Pending</a>
                                                        </li>
                                                    @endif
                                                    @if ($order->order_status != 2 && $order->order_status != 5)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.order.status.update', ['status' => 2, 'order_ids[]' => $order->id]) }}">Processing</a>
                                                        </li>
                                                    @endif
                                                    @if ($order->order_status != 3 && $order->order_status != 5)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.order.status.update', ['status' => 3, 'order_ids[]' => $order->id]) }}">On
                                                                The Way</a></li>
                                                    @endif
                                                    @if ($order->order_status != 4 && $order->order_status != 5)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.order.status.update', ['status' => 4, 'order_ids[]' => $order->id]) }}">Cancel
                                                                Order</a></li>
                                                    @endif
                                                    @if ($order->order_status != 5)
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.order.status.update', ['status' => 5, 'order_ids[]' => $order->id]) }}">
                                                                Complete
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if ($order->order_status != 5)
                                                        <li>
                                                            <button type="button" class="dropdown-item"
                                                                onclick="showAllocateToRiderModal('{{ $order->id }}')">
                                                                Allocate to Rider
                                                            </button>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>




        <div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allocateModalLabel">Allocate to Rider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="allocateForm">
                            @csrf
                            <input type="hidden" name="order_id" id="order_id">
                            <div class="mb-3">
                                <label for="riderSelect" class="form-label">Select Rider</label>
                                <select class="form-select" id="rider_id" name="rider_id" required>
                                    <option value="" disabled selected>Choose a Rider</option>
                                    @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="orderPaymentMethod" class="form-label">Payment</label>
                                <select class="form-select" id="orderPaymentMethod" name="orderPaymentMethod" required>
                                    <option value="1">Cash On Delivery</option>
                                    <option value="2">Online</option>
                                    <option value="3">Online Without Delivery Charges</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm w-100"
                                id="allocateButton">Allocate</button>
                        </form>
                    </div>
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
        $('#byRider').select2();

        function showAllocateToRiderModal(orderId) {

            $('#order_id').val(orderId);
            $('#allocateModal').modal('show');
        }

        $(document).ready(function() {
            var table = $('#example').DataTable();

            $('#select_all').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('input[name="order_ids[]"]').prop('checked', isChecked);
            });


            $('#allocateButton').on('click', function() {
                var order_id = $('#order_id').val();
                var rider_id = $('#rider_id').val();
                var orderPaymentMethod = $('#orderPaymentMethod').val();
                if (rider_id == null) {
                    alert('Please select a rider');
                    return false;
                }
                $.ajax({
                    url: '{{ route('admin.order.allocate.rider') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_id: order_id,
                        rider_id: rider_id,
                        pay_method: orderPaymentMethod,
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Order has been allocated to the rider successfully');
                            location.reload();
                        } else {
                            alert('Failed to allocate order to the rider');
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });

      
        function submitOrdersMultiActionForm(ordersAction) {
            $('#ordersAction').val(ordersAction);
            $('#ordersMultiActionForm').submit();
        }

        function toggleOrdersActionButtonsRow() {
            if ($('input[name="order_ids[]"]:checked').length === 0) {
                $('#ordersActionButtonsRow').slideUp('fast');
            } else {
                $('#ordersActionButtonsRow').slideDown('fast');
            }
            $('#ordersAction').val('');
        }
    </script>
@endsection
