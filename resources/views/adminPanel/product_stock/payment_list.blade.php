@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title">Payment Voucher List</div>

            <div class="ms-auto">
                <div class="btn-group">
                    <div class="d-flex gap-3 mt-3">
                        <button type="button" class="btn btn-primary" onclick="showDetailModelWithoutParamerter('admin/addPaymentVoucherForm','Add Payment Voucher Form')">
                            <i class="lni lni-circle-plus"></i> Add Payment Voucher
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Bank Name</th>
                            <th>Account Number</th>
                            <th>Cash Name</th>
                            <th>Supplier Name</th>
                            <th>Phone</th>
                            <th>Payment Date</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentList as $key => $plRow)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$plRow->bank_name ?? '-'}}</td>
                                    <td>{{$plRow->account_number ?? '-'}}</td>
                                    <td>{{$plRow->cash_name ?? '-'}}</td>
                                    <td>{{$plRow->supplier_name}}</td>
                                    <td>{{$plRow->supplier_phone_one}}</td>
                                    <td>{{$plRow->payment_date}}</td>
                                    <td class="text-right">{{number_format($plRow->amount)}}</td>
                                    <td>{{$plRow->description}}</td>
                                    <td>
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                            </button>
                                            <ul class="dropdown-menu" style="">
                                                <li><a onclick="showDetailModelOneParamerter('admin/viewPaymentVoucherDetail','<?php echo $plRow->id; ?>','View Payment Voucher Detail')" class="dropdown-item">
                                                        <i class="lni lni-printer" style="font-size: 18px;color: #008cff;"></i>Payment Voucher</a></li>
                                            </ul>
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
    <link href="{{asset('assets/adminPanel')}}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
@endsection
@section('js_plugins')

    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#example').DataTable({});
        });
    </script>
    <script>
        $(document).ready(function () {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
