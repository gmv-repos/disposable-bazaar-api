@php
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
@endphp

@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">
    <form id="list_data" method="get" action="{{ route('admin.report.customerLedger') }}">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <label>Customer Name</label>
                <select name="filterCustomerId" id="filterCustomerId" class="form-control select2">
                    @foreach ($posCustomers as $pcRow)
                        <option value="{{ $pcRow->id }}">{{ $pcRow->name }} - {{ $pcRow->email }} - {{ $pcRow->phone }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-sm-2 col-xs-12">
                <label>From Date</label>
                <input type="date" name="filterFromDate" id="filterFromDate" value="{{ $fromDate }}" class="form-control">
            </div>
            <div class="col-lg-2 col-sm-2 col-xs-12">
                <label>&nbsp;</label>
                <input type="text" readonly value="Between" class="form-control">
            </div>
            <div class="col-lg-2 col-sm-2 col-xs-12">
                <label>To Date</label>
                <input type="date" name="filterToDate" id="filterToDate" value="{{ $endDate }}" class="form-control">
            </div>
            <div class="col-lg-2 col-sm-2 col-xs-12" style="padding: 20px;">
                <input type="button" value="Filter" onclick="get_ajax_data()" class="btn btn-xs btn-success" />
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-end mb-2">
        {{-- PDF Button --}}
        <form id="pdf_form" method="GET" action="{{ route('admin.report.customerLedger') }}" target="_blank" class="me-2">
            <input type="hidden" name="action" value="pdf">
            <input type="hidden" name="filterCustomerId">
            <input type="hidden" name="filterFromDate">
            <input type="hidden" name="filterToDate">
            <button type="submit" class="btn btn-secondary">
                <i class="lni lni-printer"></i> PDF
            </button>
        </form>

        {{-- Excel Button --}}
        <form id="excel_form" method="GET" action="{{ route('admin.report.customerLedger') }}" target="_blank" class="me-2">
            <input type="hidden" name="action" value="excel">
            <input type="hidden" name="filterCustomerId">
            <input type="hidden" name="filterFromDate">
            <input type="hidden" name="filterToDate">
            <button type="submit" class="btn btn-success">
                <i class="lni lni-exit"></i> Excel
            </button>
        </form>

        {{-- Print Button --}}
        {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-Customer-ledger-report') !!}
    </div>

    <div class="card">
        <div class="card-body">
            <div id="printable-Customer-ledger-report">
                <table id="dtReport" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">S.NO.</th>
                            <th class="text-center">Voucher No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Receipt</th>
                            <th class="text-center">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="data">
                        {{-- AJAX data will be loaded here --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function get_ajax_data() {
        var form = $('#list_data');
        var actionUrl = form.attr('action');
        $('#data').html('<tr><td colspan="100"><div class="loader"></div></td></tr>');
        $.ajax({
            type: "GET",
            url: actionUrl,
            data: form.serialize(),
            async: true,
            cache: false,
            success: function (data) {
                $('#data').html(data);
                dtReportTableInit();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    $(document).ready(function () {
        get_ajax_data();
    });

    function dtReportTableInit() {
        if ($.fn.DataTable.isDataTable('#dtReport')) {
            $('#dtReport').DataTable().destroy();
        }

        $('#dtReport').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-4'l><'col-md-4 text-center'f><'col-md-4 text-end'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 text-end'p>>",
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    }

    // Sync PDF form values
    document.getElementById('pdf_form').addEventListener('submit', function (e) {
        const customerId = document.getElementById('filterCustomerId').value;
        const fromDate = document.getElementById('filterFromDate').value;
        const toDate = document.getElementById('filterToDate').value;

        this.querySelector('[name="filterCustomerId"]').value = customerId;
        this.querySelector('[name="filterFromDate"]').value = fromDate;
        this.querySelector('[name="filterToDate"]').value = toDate;
    });

    // Sync Excel form values
    document.getElementById('excel_form').addEventListener('submit', function (e) {
        const customerId = document.getElementById('filterCustomerId').value;
        const fromDate = document.getElementById('filterFromDate').value;
        const toDate = document.getElementById('filterToDate').value;

        this.querySelector('[name="filterCustomerId"]').value = customerId;
        this.querySelector('[name="filterFromDate"]').value = fromDate;
        this.querySelector('[name="filterToDate"]').value = toDate;
    });
</script>
@endsection
