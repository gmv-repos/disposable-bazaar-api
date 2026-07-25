@extends('adminPanel.layout.layout')

@section('title', 'Quotations List')

@section('main_content')

<div class="page-content">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Quotations List</h4>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
                Create Quotation
            </a>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            {{-- <th>Delivery Charges</th> --}}
                            <th>Payable Amount</th>
                            {{-- <th>Note</th> --}}
                            <th>Valid Until</th>
                            {{-- <th>Status</th> --}}
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotations as $quotation)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $quotation->reference_code }}</td>
                            <td>{{ $quotation->customer_name ?? '-' }}</td>
                            <td>{{ $quotation->total }}</td>
                            <td>{{ $quotation->discount }}</td>
                            <td>{{ $quotation->tax }}</td>
                            {{-- <td>{{ $quotation->delivery_charges }}</td> --}}
                            <td>{{ $quotation->payable_amount }}</td>
                            {{-- <td>{{ $quotation->notes }}</td> --}}
                            <td>{{ dateFormat($quotation->valid_until) }}</td>
                            {{-- <td>{{ $quotation->status }}</td> --}}
                            <td>
                                @if($quotation->status == 'Pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($quotation->status == 'Accepted')
                                <span class="badge bg-success">Accepted</span>
                                @elseif($quotation->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @endif
                            <td class="d-flex">

                                <a href="{{ route('quotations.quotationInvoice', $quotation->id) }}" class="btn btn-sm btn-secondary mx-1">
                                    <i class="lni lni-printer"></i>
                                </a>
                                <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-info mx-1">
                                    <i class="lni lni-eye"></i>
                                </a>
                                @if($quotation->status == 'Pending')
                                <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-warning mx-1">
                                    <i class="lni lni-pencil"></i>
                                </a>
                                @endif
                                <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mx-1">
                                        <i class="lni lni-trash"></i>
                                    </button>
                                </form>
                            </td>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });
</script>
@endsection