@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card">
            <div class="card-body">

                <h6 class="h6">Filters</h6>
                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('sell.list') }}">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm w-100" name="byRider" id="byRider">
                                        <option value="">All Rider</option>
                                        @foreach ($riders as $rider)
                                            <option value="{{ $rider->id }}"
                                                {{ ixiSelected($rider->id, request('byRider')) }}>
                                                {{ $rider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="from_date" name="from_date"
                                        class="form-control form-control-sm" value="{{ old('from_date', $from_date) }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="to_date" name="to_date" class="form-control form-control-sm"
                                        value="{{ old('to_date', $to_date) }}">
                                </div>

                                <div class="col-md-3 d-flex">
                                    <button type="button" class="btn btn-success btn-sm w-50" onclick="this.form.submit()">
                                        Filter
                                    </button>
                                    <a href="{{ route('sell.list') }}" class="btn btn-info btn-sm w-50 ms-2">
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <hr class="fw-bold my-3" />
                </div>

                <form action="{{ route('sell.sellsMultiAction') }}" method="POST" id="sellsMultiActionForm">
                    @csrf
                    <input type="hidden" name="sellsAction" id="sellsAction">
                    <h6 class="h6">Actions</h6>
                    <div class="row align-items-center justify-content-between g-2 mb-3" id="sellsActionButtonsRow"
                        style="display: none">
                        <div class="col-md-5 d-flex bg-light gap-3 border py-1">
                            <select class="form-select form-select-sm" id="riderID" name="riderID">
                                <option value="" selected>Rider</option>
                                @foreach ($riders as $rider)
                                    <option value="{{ $rider->id }}">
                                        {{ $rider->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select class="form-select form-select-sm w-25" id="paymentType" name="paymentType">
                                <option value="" selected>Pay Method</option>
                                <option value="0">COD</option>
                                <option value="1">Online</option>
                            </select>

                            <button type="button" class="btn btn-success btn-sm w-25"
                                onclick="submitSellsMultiActionForm('allocateRiderAndPayType')">
                                Allocate
                            </button>
                        </div>

                        <div class="col-md-4 d-flex">
                            <select class="form-select form-select-sm" name="payStatus">
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                            <button type="button" class="btn btn-success btn-sm w-25 ms-2"
                                onclick="submitSellsMultiActionForm('riderPayStatus')">
                                Change
                            </button>
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="button" class="btn btn-info btn-sm w-50"
                                onclick="submitSellsMultiActionForm('print')">
                                Print
                            </button>
                            <button type="button" class="btn btn-danger btn-sm w-50"
                                onclick="submitSellsMultiActionForm('downloadPDF')">
                                Download PDF
                            </button>
                        </div>
                        <hr class="fw-bold my-3" />
                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table-striped table-bordered table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>-</th>
                                    <th>S.NO.</th>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Sell Amount</th>
                                    <th>Payment Date</th>
                                    <th>Rider</th>
                                    <th>Pay Method</th>
                                    <th>Pay Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sellList as $key => $sell)
                                    <tr style="@if ($sell->order_status == 7) background-color: red @endif">
                                        <td class="text-center">
                                            <input type="checkbox" name="sellIDs[]" value="{{ $sell->id }}"
                                                onchange="toggleSellsActionButtonsRow()">
                                        </td>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ dateFormat($sell->created_at) }}</td>
                                        <td>{{ $sell->customer->name }}</td>
                                        <td>{{ $sell->customer->phone }}</td>
                                        <td class="text-right">{{ number_format($sell->total_payable_amount, 0) }}</td>
                                        <td>
                                            {{ dateFormat($sell->payment_date) }}
                                        </td>
                                        <td>
                                            @if (!is_null($sell->rider_id))
                                                <a href="{{ route('riders.show', $sell->rider_id) }}">
                                                    {{ $sell->rider->name }}
                                                </a>
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
                                                <span class="badge bg-success px-3">
                                                    Paid to {{ $sell->pay_method == 1 ? 'company' : 'rider' }}
                                                </span>
                                            @elseif($sell->rider_pay_status == 'unpaid')
                                                <span class="badge bg-warning px-3">
                                                    Unpaid to {{ $sell->pay_method == 2 ? 'rider' : 'company' }}
                                                </span>
                                            @else
                                                <span class="badge bg-light px-3">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="dropdown d-flex justify-content-center">
                                                <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                                </button>
                                                <ul class="dropdown-menu" style="">

                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                            onclick="showAllocateToRiderModal('{{ $sell->id }}')">
                                                            <i class="lni lni-delivery"
                                                                style="font-size: 18px;color: #008cff;"></i>
                                                            Allocate to Rider
                                                        </button>
                                                    </li>
                                                    <li><a href="{{ route('sell.invoice', ['id' => $sell->id]) }}"
                                                            class="dropdown-item" target="_blank">
                                                            <i class="lni lni-printer"
                                                                style="    font-size: 18px;color: #008cff;"></i>
                                                            Invoice print</a>
                                                    </li>
                                                    @if ($sell->order_status != 7)
                                                        <!-- <li>
                                                                              <a onclick="showDetailModelOneParamerter('admin/addReturnInvoiceForm','{{ $sell->id }}','Add Return Invoice Form')" class="dropdown-item">
                                                                               <i class="lni lni-printer" style="font-size: 18px;color: #008cff;"></i>Return Invoice
                                                                               </a>
                                                                        </li> -->
                                                        {{-- <li class="align-items-center"
                                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                                            <a class="dropdown-item" href="#">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-trash text-primary">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                </svg>
                                                                Delete</a>
                                                        </li> --}}
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
        {{-- modal --}}

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
                            <input type="hidden" name="sell_id" id="sell_id">
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
                                <label for="sellPaymentMethod" class="form-label">Payment</label>
                                <select class="form-select" id="sellPaymentMethod" name="sellPaymentMethod" required>
                                    <option value="1">Cash On Delivery</option>
                                    <option value="2">Online</option>
                                    <option value="3">Online Without Delivery Charges</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm w-100" id="allocateButton">
                                Allocate
                            </button>
                        </form>
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
            $('#example').DataTable({});
        });


        $('#allocateButton').on('click', function() {
            var sell_id = $('#sell_id').val();
            var rider_id = $('#rider_id').val();
            var sellPaymentMethod = $('#sellPaymentMethod').val();
            if (rider_id == null) {
                warningToast('Please select a rider');
                return false;
            }
            $.ajax({
                url: "{{ route('pos.sellAllocateRider') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sell_id: sell_id,
                    rider_id: rider_id,
                    pay_method: sellPaymentMethod,
                },
                success: function(response) {
                    if (response.success) {
                        $('#allocateModal').modal('hide');
                        successToast('Sell has been allocated to the rider successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        errorToast('Failed to allocate order to the rider');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });


        });

        $(document).ready(function() {

            $('#byRider').select2();
            $('#riderID').select2({
                width: '50%'
            });

            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });

        function showAllocateToRiderModal(sell_id) {
            $('#sell_id').val(sell_id);
            $('#allocateModal').modal('show');
        }

        function toggleSellsActionButtonsRow() {
            if ($('input[name="sellIDs[]"]:checked').length === 0) {
                $('#sellsActionButtonsRow').slideUp('fast');
            } else {
                $('#sellsActionButtonsRow').slideDown('fast');
            }
            $('#sellsAction').val('');
        }

        function submitSellsMultiActionForm(sellsAction) {
            $('#sellsAction').val(sellsAction);
            $('#sellsMultiActionForm').submit();
        }
    </script>
@endsection
