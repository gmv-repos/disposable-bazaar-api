@extends('adminPanel.layout.layout')

@section('title', 'Expenses List')

@section('main_content')

<div class="page-content">
    <!-- Expense summary card -->

    <!-- Expenses table -->
    <div class="card">
        <div class="card-body p-4">

            <div class="row mb-4 gx-4">
                <div class="col-md-4 bg-white border py-2 rounded">
                    <b>Today's Expenses</b>
                    <p>{{ number_format($todayExpense, 2) }}</p>
                </div>
                <div class="col-md-4 bg-white border py-2 rounded">
                    <b>This Month's Expenses</b>
                    <p>{{ number_format($monthExpense, 2) }}</p>
                </div>
                <div class="col-md-4 bg-white border py-2 rounded">
                    <b>Total Expenses</b>
                    <p>{{ number_format($totalExpense, 2) }}</p>
                </div>
            </div>
            <!-- Filter Form -->
            <form method="GET" action="{{ route('expenses.index') }}" class="row mb-4 gx-4">
                <div class="col-md-3">
                    <label>From Date:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>To Date:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>Expense Account:</label>
                    <select name="expense_account_id" class="form-control">
                        <option value="">All Accounts</option>
                        @foreach ($expenseAccounts as $account)
                            <option value="{{ $account->id }}" {{ $expenseAccountId == $account->id ? 'selected' : '' }}>
                                {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Expense Account</th>
                            <th>Bank Account</th>
                            <th>Account Number</th>
                            <th>Cash Account</th>
                            <th>Expense Date</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->id }}</td>
                            <td>{{ $expense->expense_account_name ?? '-' }}</td>
                            <td>{{ $expense->bank_name ?? '-' }}</td>
                            <td>{{ $expense->account_number ?? '-' }}</td>
                            <td>{{ $expense->cash_name ?? '-' }}</td>
                            <td>{{ $expense->expense_date }}</td>
                            <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->description ?? 'N/A' }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <!-- Edit Action -->
                                        @if($expense->status == 1)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('expenses.edit', $expense->id) }}">
                                                    <i class="lni lni-pencil"></i>
                                                    Edit
                                                </a>
                                            </li>

                                            <li>
                                                <form action="{{ route('expense.expenseInactive', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to inactive this expense?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="lni lni-trash"></i>
                                                        Inactive
                                                    </button>
                                                </form>
                                            </li>
                                        @else
                                            <li>
                                                <form action="{{ route('expense.expenseActive', $expense->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to active this expense?');">
                                                    @csrf
                                                    @method('DELETE')
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