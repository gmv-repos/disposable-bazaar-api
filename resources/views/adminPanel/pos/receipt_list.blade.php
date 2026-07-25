@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title">Receipt Voucher List</div>

            <div class="ms-auto">
                <div class="btn-group">
                    <div class="d-flex gap-3 mt-3">
                        <button type="button" class="btn btn-primary" onclick="showDetailModelWithoutParamerter('admin/addReceiptVoucherForm','Add Receipt Voucher Form')">
                            <i class="lni lni-circle-plus"></i> Add Receipt Voucher
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
                                <th>Customer Name</th>
                                <th>Mobile No</th>
                                <th>Receipt Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receiptList as $key => $rlRow)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$rlRow->bank_name ?? '-'}}</td>
                                    <td>{{$rlRow->account_number ?? '-'}}</td>
                                    <td>{{$rlRow->cash_name ?? '-'}}</td>
                                    <td>{{$rlRow->name}}</td>
                                    <td>{{$rlRow->phone}}</td>
                                    <td>{{$rlRow->receipt_date}}</td>
                                    <td class="text-right">{{number_format($rlRow->amount)}}</td>
                                    <td>{{$rlRow->description}}</td>
                                    <td>
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                            </button>
                                            <ul class="dropdown-menu" style="">
                                                <li><a onclick="showDetailModelOneParamerter('admin/viewReceiptVoucherDetail','<?php echo $rlRow->id; ?>','View Receipt Voucher Detail')" class="dropdown-item">
                                                        <i class="lni lni-printer" style="font-size: 18px;color: #008cff;"></i>Receipt Voucher</a></li>
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
        function editSupplierInfo(id) {
            var url_link="{{route('supplier.edit.info')}}"
            $.ajax({
                url: url_link,
                type: "get",
                data: {
                    id:id,
                },
                success: function(response) {
                    $('#updateinfo').html(response)
                    $('#supplier_edit').modal('show')
                },
                error: function(xhr) {
                    //Do Something to handle error
                }});


        }

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
