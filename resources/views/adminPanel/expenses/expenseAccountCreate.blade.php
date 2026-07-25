@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">

    <div class="card">
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{ route('expenses.expenseAccountstore') }}" method="POST">
                    @csrf
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label>Parent Expense Account Head:</label>
                        <span class="rflabelsteric"><strong>*</strong></span>
                        <select autofocus="autofocus" class="form-control requiredField" name="parent_id" id="parent_id">
                            <option value="">Select Expense Account</option>
                            @foreach($expenseAccounts as $key => $y)
                                <option value="{{ $y->id}}">{{ $y->account_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="acc_name">New Account </label>
                        <span class="rflabelsteric"><strong>*</strong></span>
                        <input type="text" required  placeholder="New Account" class="form-control requiredField" name="account_name" id="account_name" value="" autocomplete="off" >
                    </div>
                    <div>&nbsp;</div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button type="submit" class="btn btn-primary">Create Expense Account</button>
                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
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
    </script>
@endsection