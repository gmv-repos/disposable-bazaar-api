@extends('adminPanel.layout.layout')

@section('css')
<style>
    /* Add any custom styles here if needed */
</style>
@endsection

@section('main_content')
<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h5>Discount Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Product Variant -->
                <div class="col-md-6 mb-3">
                    <h6>Name</h6>
                    <p>
                        {{ $discount->name }} <br>                        
                    </p>
                </div>

                <!-- Discount Percentage -->
                <div class="col-md-6 mb-3">
                    <h6>Discount Percentage</h6>
                    <p>{{ $discount->discount_percentage }}%</p>
                </div>

                <!-- Start Time -->
                <div class="col-md-6 mb-3">
                    <h6>Start Time</h6>
                    <p>{{ \Carbon\Carbon::parse($discount->start_time)->format('d M, Y h:i A') }}</p>
                </div>

                <!-- End Time -->
                <div class="col-md-6 mb-3">
                    <h6>End Time</h6>
                    <p>{{ \Carbon\Carbon::parse($discount->end_time)->format('d M, Y h:i A') }}</p>
                </div>

                <!-- Is Active -->
                <div class="col-md-6 mb-3">
                    <h6>Status</h6>
                    <p>
                        @if($discount->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </p>
                </div>

                <!-- Created At -->
                <div class="col-md-6 mb-3">
                    <h6>Created At</h6>
                    <p>{{ \Carbon\Carbon::parse($discount->created_at)->format('d M, Y h:i A') }}</p>
                </div>

                <!-- Updated At -->
                <div class="col-md-6 mb-3">
                    <h6>Last Updated</h6>
                    <p>{{ \Carbon\Carbon::parse($discount->updated_at)->format('d M, Y h:i A') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('discounts.index') }}" class="btn btn-secondary btn-sm">
                    <i class="lni lni-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-primary btn-sm">
                    <i class="lni lni-pencil"></i> Edit
                </a>
                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                        <i class="lni lni-trash"></i> Delete
                    </button>
                </form>
            </div>
            <div class="mt-4">
                <h5>Discounted Items on <strong> {{$discount->name}}</strong> </h5>


            <table cellpadding="10" width="100%" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discount->item as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection