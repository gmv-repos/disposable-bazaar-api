@extends('adminPanel.layout.layout')

@section('title', 'Rider Payments')

@section('main_content')

<div class="page-content">

    <div class="card">
        <div class="card-body p-4">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('rider.payments.index') }}" class="row mb-4 gx-4">
                <div class="col-md-3">
                    <label>From Date:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>To Date:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>Riders</label>
                    <select name="rider_id" class="form-control">
                        <option value="">All Riders</option>
                        @foreach ($riders as $rider)
                            <option value="{{ $rider->id }}">
                                {{ $rider->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
            <div>
                <a href="{{ route('rider.payments.create') }}" class="btn btn-primary btn-sm px-4">
                    Add Payment
                </a>
            </div>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Payment Date</th>
                            <th>Rider</th>
                            <th>Bank Account</th>
                            <th>Account Number</th>
                            <th>Cash Account</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->payment_date }}</td>
                            <td>{{ $payment->rider->name ?? '-' }}</td>
                            <td>{{ $payment->bankAccount->account_name ?? '-' }}</td>
                            <td>{{ $payment->bankAccount->account_number ?? '-' }}</td>
                            <td>{{ $payment->cashAccount->account_name ?? '-' }}</td>
                            <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->description ?? 'N/A' }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <!-- Edit Action -->
                                        @if($payment->status == 1)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('rider.payments.edit', $payment->id) }}">
                                                    <i class="lni lni-pencil"></i>
                                                    Edit
                                                </a>
                                            </li>

                                            <li>
                                                <form action="{{ route('rider.payments.toggleStatus', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to inactive this expense?');">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="lni lni-trash"></i>
                                                        Inactive
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('rider.payments.toggleStatus', $payment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to active this expense?');">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="lni lni-trash"></i>
                                                        Active
                                                    </button>
                                                </form>
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
        </div>
    </div>
</div>

@endsection