@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">

    <div class="card">
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expense_account_id" class="form-label">Expense Account</label>
                            <select
                                name="expense_account_id"
                                id="expense_account_id"
                                class="form-select"
                                required>
                                <option value="">Select Expense Account</option>
                                @foreach ($expenseAccounts as $eaRow)
                                <option value="{{  $eaRow->id }}" @if($eaRow->id == $expense->expense_account_id) selected @endif>
                                {{ $eaRow->parent->account_name ?? '-' }} / {{ $eaRow->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expense_date" class="form-label">Expense Date</label>
                            <input
                                type="date"
                                name="expense_date"
                                id="expense_date"
                                class="form-control"
                                value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}"
                                required>
                            @error('expense_date')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input
                                type="number"
                                name="amount"
                                id="amount"
                                class="form-control"
                                placeholder="Enter amount"
                                value="{{ old('amount', $expense->amount) }}"
                                step="0.01"
                                required>
                            @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expense_account_id" class="form-label">Bank Account</label>
                            <select
                                name="bank_account_id"
                                id="bank_account_id"
                                class="form-select"
                                required onchange="togglePaymentOptions()">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankAccounts as $baRow)
                                <option value="{{  $baRow->id }}" @if($baRow->id == $expense->bank_account_id) selected @endif>
                                {{ $baRow->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="expense_account_id" class="form-label">Cash Account</label>
                            <select
                                name="cash_account_id"
                                id="cash_account_id"
                                class="form-select"
                                required onchange="togglePaymentOptions()">
                                <option value="">Select Cash Account</option>
                                @foreach ($cashAccounts as $caRow)
                                <option value="{{  $caRow->id }}" @if($caRow->id == $expense->cash_account_id) selected @endif>
                                {{ $caRow->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="3"
                                placeholder="Provide a brief description of the expense">{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">Update Expense</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
    <script>
        $('#expense_type').select2();


        function togglePaymentOptions() {
            if ($('#bank_account_id').val()) {
                $('#cash_account_id').prop('disabled', true);
            } else {
                $('#cash_account_id').prop('disabled', false);
            }

            if ($('#cash_account_id').val()) {
                $('#bank_account_id').prop('disabled', true);
            } else {
                $('#bank_account_id').prop('disabled', false);
            }
        }

        $(document).ready(function () {
            // Initial load
            togglePaymentOptions();
            // Event bindings
            $('#bank_account_id').on('change', togglePaymentOptions);
            $('#cash_account_id').on('change', togglePaymentOptions);
        });

    </script>
@endsection
