@extends('adminPanel.layout.layout')
@section('css')
    <style>
        img {
            display: block;
            max-width: 100%;
        }

        .preview {
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }
    </style>
@endsection
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">PR/GRNs List</h4>
                <a href="{{ route('purchase.received.create') }}" class="btn btn-primary float-end">Add New</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table-striped table-bordered table" style="width:100%">
                        <thead>
                            <tr>
                                <th>PR Code</th>
                                <th>PO Code</th>
                                <th>Supplier Name</th>
                                <th>Payable</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseReceive as $pr)
                                <tr>
                                    <td>
                                        {{ $pr->pr_code }}
                                    </td>
                                    <td>
                                        {{ $pr->purchase->purchase_code }}
                                    </td>
                                    <td>
                                        {{ $pr->supplier->supplier_name }}
                                    </td>
                                    <td>
                                        {{ $pr->payable_amount }}
                                    </td>
                                    <td class="d-flex">

                                        <a href="{{ route('purchase.received.pdf.download', $pr->id) }}"
                                            class="btn btn-sm btn-secondary mx-1">
                                            <i class="lni lni-printer"></i>
                                        </a>

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


@section('js')
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                order: []
            });
        });
    </script>
@endsection
