@php
    $fromDate = date('Y-m-d', strtotime('-30 days'));
    $endDate = date('Y-m-d');
@endphp

@extends('adminPanel.layout.layout')

@section('main_content')
    <div class="page-content">
        <form id="list_data" method="get" action="{{ route('admin.report.bankLedger') }}">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <label>Bank Name</label>
                    <select name="filterBankId" id="filterBankId" class="form-control select2">
                        @foreach ($banks as $bRow)
                            <option value="{{ $bRow->id }}">{{ $bRow->account_name }} - {{ $bRow->account_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-sm-2 col-xs-12">
                    <label>From Date</label>
                    <input type="date" name="filterFromDate" id="filterFromDate" value="{{ $fromDate }}"
                        class="form-control">
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
            {{-- PDF Export --}}
            <form id="pdf_form" method="GET" action="{{ route('admin.report.bankLedger') }}" target="_blank" class="me-2">
                <input type="hidden" name="action" value="pdf">
                <input type="hidden" name="filterBankId">
                <input type="hidden" name="filterFromDate">
                <input type="hidden" name="filterToDate">
                <button type="submit" class="btn btn-secondary">
                    <i class="lni lni-printer"></i> PDF
                </button>
            </form>

            {{-- Excel Export --}}
            <form id="excel_form" method="GET" action="{{ route('admin.report.bankLedger') }}" target="_blank" class="me-2">
                <input type="hidden" name="action" value="excel">
                <input type="hidden" name="filterBankId">
                <input type="hidden" name="filterFromDate">
                <input type="hidden" name="filterToDate">
                <button type="submit" class="btn btn-success">
                    <i class="lni lni-exit"></i> Excel
                </button>
            </form>

            {{-- Print --}}
            {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-div-id') !!}
        </div>

        <div class="card">
            <div class="card-body">
                <div id="printable-div-id">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">S.NO.</th>
                                <th class="text-center">Voucher No</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Payment</th>
                                <th class="text-center">Receipt</th>
                                <th class="text-center">Out</th>
                                <th class="text-center">In</th>
                                <th class="text-center">Extra Out</th>
                                <th class="text-center">Extra In</th>
                                <th class="text-center">Balance</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                            {{-- AJAX-loaded data --}}
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
            const form = $('#list_data');
            const actionUrl = form.attr('action');
            $('#data').html('<tr><td colspan="100"><div class="loader"></div></td></tr>');
            $.ajax({
                type: "GET",
                url: actionUrl,
                data: form.serialize(),
                async: true,
                cache: false,
                success: function (data) {
                    $('#data').html(data);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        $(document).ready(function () {
            get_ajax_data();
        });

        // Sync PDF form values before submit
        document.getElementById('pdf_form').addEventListener('submit', function (e) {
            const bankId = document.getElementById('filterBankId').value;
            const fromDate = document.getElementById('filterFromDate').value;
            const toDate = document.getElementById('filterToDate').value;

            this.querySelector('[name="filterBankId"]').value = bankId;
            this.querySelector('[name="filterFromDate"]').value = fromDate;
            this.querySelector('[name="filterToDate"]').value = toDate;
        });

        // Sync Excel form values before submit
        document.getElementById('excel_form').addEventListener('submit', function (e) {
            const bankId = document.getElementById('filterBankId').value;
            const fromDate = document.getElementById('filterFromDate').value;
            const toDate = document.getElementById('filterToDate').value;

            this.querySelector('[name="filterBankId"]').value = bankId;
            this.querySelector('[name="filterFromDate"]').value = fromDate;
            this.querySelector('[name="filterToDate"]').value = toDate;
        });
    </script>
@endsection