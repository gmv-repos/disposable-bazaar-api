@extends('adminPanel.layout.layout')

@section('title', 'Sales and Orders')

@section('main_content')
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <!-- Filters -->
            <h6 class="h6">Filters</h6>
            <form method="GET" action="{{ route('web.orders.index') }}">
                <div class="row align-items-center justify-content-center mb-3">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="status">
                            <option value="">All Orders</option>
                            <option value="1" @selected(request('status')==1)>Pending</option>
                            <option value="2" @selected(request('status')==2)>Processing</option>
                            <option value="3" @selected(request('status')==3)>On The Way</option>
                            <option value="4" @selected(request('status')==4)>Canceled</option>
                            <option value="5" @selected(request('status')==5)>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="filter_rider" id="filter_rider">
                            <option value="">All Riders</option>
                            @foreach ($riders as $rider)
                            <option value="{{ $rider->id }}"
                                {{ request('filter_rider') == $rider->id ? 'selected' : '' }}>
                                {{ $rider->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control form-control-sm"
                            value="{{ $from_date }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control form-control-sm"
                            value="{{ $to_date }}">
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-success btn-sm w-50">Filter</button>
                        <a href="{{ route('web.orders.index') }}" class="btn btn-info btn-sm w-50 ms-2">Clear</a>
                    </div>
                </div>
            </form>
            <hr class="fw-bold my-3" />


            <div class="table-responsive">
                <table class="table-striped table-bordered table" style="width:100%" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center"><input type="checkbox" id="select_all_orders"></th>
                            <th>S.NO.</th>
                            <th>Date</th>
                            <th>Order No</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Rider</th>
                            <th>Pay Method</th>
                            <th>Order Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- E-commerce Orders Tab -->
                        <form method="POST" action="{{ route('admin.order.ordersMultiAction') }}"
                            id="ordersMultiActionForm">
                            @csrf
                            <input type="hidden" name="ordersAction" id="ordersAction">
                            <div class="row align-items-center justify-content-between mb-3" id="ordersActionButtonsRow"
                                style="display: none">
                                <div class="col-md-4 d-flex align-items-center">
                                    <select class="form-select form-select-sm" name="ordersStatus">
                                        <option value="1">Pending</option>
                                        <option value="2">Processing</option>
                                        <option value="3">On The Way</option>
                                        <option value="4">Canceled</option>
                                        <option value="5">Completed</option>
                                    </select>
                                    <button type="button" class="btn btn-success btn-sm w-50 ms-1"
                                        onclick="submitOrdersMultiActionForm('changeOrderStatus')">Change</button>
                                </div>
                                <div class="col-md-5 d-flex gap-3">
                                    <select class="form-select form-select-sm w-50" id="riderID" name="riderID">
                                        <option value="" selected>Rider</option>
                                        @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select form-select-sm w-25" name="paymentMethod">
                                        <option value="" selected>Pay Method</option>
                                        <option value="1">COD</option>
                                        <option value="2">Online</option>
                                    </select>
                                    <button type="button" class="btn btn-success btn-sm w-25"
                                        onclick="submitOrdersMultiActionForm('allocateRiderAndPayMethod')">Allocate</button>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="button" class="btn btn-info btn-sm w-50"
                                        onclick="submitOrdersMultiActionForm('print')">Print</button>
                                    <button type="button" class="btn btn-danger btn-sm w-50"
                                        onclick="submitOrdersMultiActionForm('downloadPDF')">Download PDF</button>
                                </div>
                            </div>
                            <hr class="fw-bold my-3" />

                            @foreach ($orderList as $key => $order)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}"
                                        onchange="toggleOrdersActionButtonsRow()">
                                </td>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ dateFormat($order->created_at) }}</td>
                                <td># {{ $order->order_no }}</td>
                                <td>{{ is_null($order->customer_id) ? 'Guest' : $order->customer->name }}
                                </td>
                                <td>{{ $order->customer->phone ?? '' }}</td>
                                <td>{{ round($order->total_amount) }}</td>
                                <td>{{ round($order->grand_total) }}</td>
                                <td>
                                    @if (!is_null($order->rider_id))
                                    <a
                                        href="{{ route('riders.show', $order->rider_id) }}">{{ $order->rider->name }}</a>
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
                                <td>{!! getOrderStatusBadge($order->order_status) !!}</td>
                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary dropdown-toggle btn-sm" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">Settings</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('admin.order.detail', ['order_id' => $order->id]) }}">Order
                                                    Detail</a></li>
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
                                            <li><a class="dropdown-item"
                                                    href="{{ route('admin.order.status.update', ['status' => 5, 'order_ids[]' => $order->id]) }}">Complete</a>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item"
                                                    onclick="showAllocateToRiderModal('order', '{{ $order->id }}')">
                                                    Allocate to Rider
                                                </button>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </form>

                        <!-- POS Sells Tab -->
                        <form action="{{ route('sell.sellsMultiAction') }}" method="POST"
                            id="sellsMultiActionForm">
                            @csrf
                            <input type="hidden" name="sellsAction" id="sellsAction">
                            <div class="row align-items-center justify-content-between g-2 mb-3"
                                id="sellsActionButtonsRow" style="display: none">
                                <div class="col-md-5 d-flex bg-light gap-3 border py-1">
                                    <select class="form-select form-select-sm" id="riderID_sell" name="riderID">
                                        <option value="" selected>Rider</option>
                                        @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select form-select-sm w-25" name="paymentType">
                                        <option value="" selected>Pay Method</option>
                                        <option value="0">COD</option>
                                        <option value="1">Online</option>
                                    </select>
                                    <button type="button" class="btn btn-success btn-sm w-25"
                                        onclick="submitSellsMultiActionForm('allocateRiderAndPayType')">Allocate</button>
                                </div>
                                <div class="col-md-4 d-flex">
                                    <select class="form-select form-select-sm" name="payStatus">
                                        <option value="paid">Paid</option>
                                        <option value="unpaid">Unpaid</option>
                                    </select>
                                    <button type="button" class="btn btn-success btn-sm w-25 ms-2"
                                        onclick="submitSellsMultiActionForm('riderPayStatus')">Change</button>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="button" class="btn btn-info btn-sm w-50"
                                        onclick="submitSellsMultiActionForm('print')">Print</button>
                                    <button type="button" class="btn btn-danger btn-sm w-50"
                                        onclick="submitSellsMultiActionForm('downloadPDF')">Download PDF</button>
                                </div>
                            </div>
                            <hr class="fw-bold my-3" />


                            @foreach ($sellList as $key => $sell)
                            <tr style="@if ($sell->order_status == 7) background-color: red @endif">
                                <td class="text-center">
                                    <input type="checkbox" name="sellIDs[]" value="{{ $sell->id }}"
                                        onchange="toggleSellsActionButtonsRow()">
                                </td>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ dateFormat($sell->created_at) }}</td>
                                <td>{{ $sell->invoice_id }}</td>
                                <td>{{ $sell->customer->name }}</td>
                                <td>{{ $sell->customer->phone }}</td>
                                <td class="text-right">{{ number_format($sell->total_payable_amount, 0) }}
                                </td>
                                <td>{{ dateFormat($sell->payment_date) }}</td>
                                <td>
                                    @if (!is_null($sell->rider_id))
                                    <a
                                        href="{{ route('riders.show', $sell->rider_id) }}">{{ $sell->rider->name }}</a>
                                    @else
                                    <span>N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sell->pay_method == 1)
                                    <span class="badge bg-info">Cash On Delivery</span>
                                    @elseif($sell->pay_method == 2)
                                    <span class="badge bg-success">Online Payment</span>
                                    @else
                                    <span>-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($sell->rider_pay_status == 'paid')
                                    <span class="badge bg-success px-3">Paid to
                                        {{ $sell->pay_method == 1 ? 'company' : 'rider' }}</span>
                                    @elseif($sell->rider_pay_status == 'unpaid')
                                    <span class="badge bg-warning px-3">Unpaid to
                                        {{ $sell->pay_method == 2 ? 'rider' : 'company' }}</span>
                                    @else
                                    <span class="badge bg-light px-3">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary dropdown-toggle btn-sm" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">Settings</button>
                                        <ul class="dropdown-menu">
                                            <li><button type="button" class="dropdown-item"
                                                    onclick="showAllocateToRiderModal('sell', '{{ $sell->id }}')">Allocate
                                                    to Rider</button></li>
                                            <li><a href="{{ route('sell.invoice', ['id' => $sell->id]) }}"
                                                    class="dropdown-item" target="_blank">Invoice
                                                    Print</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </form>
                    </tbody>
                </table>
            </div>
            <!-- Allocate Modal -->
            <div class="modal fade" id="allocateModal" tabindex="-1" aria-labelledby="allocateModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="allocateModalLabel">Allocate to Rider</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="allocateForm">
                                @csrf
                                <input type="hidden" name="item_id" id="item_id">
                                <input type="hidden" name="item_type" id="item_type">
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
                                    <label for="paymentMethod" class="form-label">Payment</label>
                                    <select class="form-select" id="paymentMethod" name="paymentMethod" required>
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
    </div>
</div>
@endsection

@section('js_plugins')
<script src="{{ asset('assets/adminPanel/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/adminPanel/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/adminPanel/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Initialize DataTables

        $('#dataTable').DataTable({
            length: 10,
        })
        // Initialize Select2
        $('#filter_rider, #riderID, #riderID_sell').select2();

        // Select all checkboxes
        $('#select_all_orders').on('change', function() {
            $('input[name="order_ids[]"]').prop('checked', $(this).is(':checked'));
            toggleOrdersActionButtonsRow();
        });

        $('#select_all_sells').on('change', function() {
            $('input[name="sellIDs[]"]').prop('checked', $(this).is(':checked'));
            toggleSellsActionButtonsRow();
        });

        // Allocate button click
        $('#allocateButton').on('click', function() {
            var item_id = $('#item_id').val();
            var item_type = $('#item_type').val();
            var rider_id = $('#rider_id').val();
            var paymentMethod = $('#paymentMethod').val();

            if (!rider_id) {
                alert('Please select a rider');
                return;
            }

            $.ajax({
                url: "{{ route('web.orders.allocateRider') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    item_type: item_type,
                    item_id: item_id,
                    rider_id: rider_id,
                    pay_method: paymentMethod,
                },
                success: function(response) {
                    if (response.success) {
                        $('#allocateModal').modal('hide');
                        alert('Successfully allocated to rider');
                        location.reload();
                    } else {
                        alert('Failed to allocate to rider');
                    }
                },
                error: function(error) {
                    console.error(error);
                    alert('An error occurred');
                }
            });
        });
    });

    function showAllocateToRiderModal(type, id) {
        $('#item_id').val(id);
        $('#item_type').val(type);
        $('#allocateModal').modal('show');
    }

    function submitOrdersMultiActionForm(action) {
        $('#ordersAction').val(action);
        $('#ordersMultiActionForm').submit();
    }

    function submitSellsMultiActionForm(action) {
        $('#sellsAction').val(action);
        $('#sellsMultiActionForm').submit();
    }

    function toggleOrdersActionButtonsRow() {
        $('#ordersActionButtonsRow').toggle($('input[name="order_ids[]"]:checked').length > 0);
        $('#ordersAction').val('');
    }

    function toggleSellsActionButtonsRow() {
        $('#sellsActionButtonsRow').toggle($('input[name="sellIDs[]"]:checked').length > 0);
        $('#sellsAction').val('');
    }
</script>
@endsection
