@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <form id="partyLedgerForm" method="GET">
                    <div class="row justify-content-between">
                    <input type="hidden" name="action" value="" id="actionInput">
                        <div class="col-md-4">
                            <select name="filterPartyId" class="form-control" id="filterPartyId">                                
                                @foreach ($parties as $party)
                                    <option value="{{ $party->id }}" {{ request()->filterPartyId == $party->id ? 'selected' : '' }}>
                                        {{ $party->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $toDate = \Carbon\Carbon::today()->format('Y-m-d');
                            $fromDate = \Carbon\Carbon::today()->subMonth()->format('Y-m-d');
                        @endphp

                        <div class="col-md-6 d-flex justify-content-between">
                            <input type="date" name="filterFromDate" value="{{ request()->filterFromDate ?? $fromDate }}"
                                class="form-control">

                            <span class="rounded bg-slate-50 mx-3 pb-0 pt-2">TO</span>

                            <input type="date" name="filterToDate" value="{{ request()->filterToDate ?? $toDate }}"
                                class="form-control">
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="setAction('search')">
                                Search
                            </button>
                            <a href="{{ route('admin.report.partyLedgerReport') }}" class="btn btn-warning">
                                Clear
                            </a>
                        </div>
                    </div>
                    </form>
                <div class="d-flex gap-2 justify-content-end mt-3">
                    <button type="button" class="btn btn-dark btn-sm px-3" onclick="setAction('pdf')">
                        PDF
                    </button>
                    <button  type="button" class="btn btn-success btn-sm px-3" onclick="setAction('excel')">
                        Excel
                    </button>
                </div>
            </div>
            <div class="card-body" id="ledgerReportContainer">

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function setAction(action){
            $('#actionInput').val(action);
            if(action == 'search'){
                searchAJAX();
            }
            else{
                $('#partyLedgerForm').submit();
            }
        }

        function searchAJAX(){
            let form = $('#partyLedgerForm');
                let url = "{{ route('admin.report.partyLedgerReport') }}";
                let container = $('#ledgerReportContainer');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: form.serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        container.html('<div class="text-center">Loading...</div>');
                    },
                    success: function (response) {
                        container.html(response.html);                    
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        container.html('<div class="alert alert-danger">Error loading ledger report.</div>');
                    }
                });
        }
        // $(document).ready(function () {

        //     $('#partyLedgerForm').on('submit', function (e) {
        //         e.preventDefault();

        //         let form = $(this);
        //         let url = "{{ route('admin.report.partyLedgerReport') }}";
        //         let container = $('#ledgerReportContainer');

        //         $.ajax({
        //             url: url,
        //             type: 'GET',
        //             data: form.serialize(),
        //             dataType: 'json',
        //             beforeSend: function () {
        //                 container.html('<div class="text-center">Loading...</div>');
        //             },
        //             success: function (response) {
        //                 container.html(response.html);                    
        //             },
        //             error: function (xhr, status, error) {
        //                 console.error(error);
        //                 container.html('<div class="alert alert-danger">Error loading ledger report.</div>');
        //             }
        //         });
        //     });        
        // });
    </script>
@endsection