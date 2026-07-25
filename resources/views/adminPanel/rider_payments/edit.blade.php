@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">

    <div class="card">
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{ route('rider.payments.update', $riderPayment->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rider_id" class="form-label">Rider</label>
                            <select
                                name="rider_id"
                                id="rider_id"
                                class="form-select"
                                required>
                                <option value="">Select Rider</option>
                                @foreach ($riders as $rider)
                                <option value="{{  $rider->id }}" @if($rider->id == $riderPayment->rider->id) selected @endif>
                                {{ $rider->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input
                                type="date"
                                name="payment_date"
                                id="payment_date"
                                class="form-control"
                                value="{{ old('payment_date', $riderPayment->payment_date->format('Y-m-d')) }}"
                                required>
                            @error('payment_date')
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
                                value="{{ old('amount', $riderPayment->amount) }}"
                                step="0.01"
                                required>
                            @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="bank_account_id" class="form-label">Bank Account</label>
                            <select
                                name="bank_account_id"
                                id="bank_account_id"
                                class="form-select"
                                required onchange="togglePaymentOptions()">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankAccounts as $baRow)
                                <option value="{{  $baRow->id }}" @if($baRow->id == $riderPayment->bank_account_id) selected @endif>
                                {{ $baRow->account_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cash_account_id" class="form-label">Cash Account</label>
                            <select
                                name="cash_account_id"
                                id="cash_account_id"
                                class="form-select"
                                required onchange="togglePaymentOptions()">
                                <option value="">Select Cash Account</option>
                                @foreach ($cashAccounts as $caRow)
                                <option value="{{  $caRow->id }}" @if($caRow->id == $riderPayment->cash_account_id) selected @endif>
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
                                placeholder="Provide a brief description of the rider payment">{{ old('description', $riderPayment->description) }}</textarea>
                            @error('description')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">Update Rider Payment</button>
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
        $('#rider_id').select2();


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
