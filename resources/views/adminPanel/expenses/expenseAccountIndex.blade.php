@extends('adminPanel.layout.layout')

@section('title', 'Expense Accounts List')

@section('main_content')

<div class="page-content">
    <!-- Expense summary card -->

    <!-- Expenses table -->
    <div class="card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parent Account</th>
                            <th>Account Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenseAccounts as $eaRow)
                        <tr>
                            <td>{{ $eaRow->id }}</td>
                            <td>{{ $eaRow->parent->account_name ?? '-' }}</td>
                            <td>{{ $eaRow->account_name }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <!-- Edit Action -->
                                        

                                        <!-- Delete Action -->
                                        @if($eaRow->status == 1)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('expense.expenseAccountEdit', $eaRow->id) }}">
                                                <i class="lni lni-pencil"></i>
                                                Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('expense.expenseAccountInactive', $eaRow->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to inactive this expense account?');">
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
                                            <form action="{{ route('expense.expenseAccountActive', $eaRow->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to active this expense account?');">
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