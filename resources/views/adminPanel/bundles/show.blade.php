@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Bundle Details</h4>
        </div>
        <div class="card-body p-4">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Reference Code</td>
                        <td>{{ $bundle->reference_code }}</td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>{{ $bundle->name }}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td>{{ $bundle->total_amount }}</td>
                    </tr>
                    <tr>
                        <td>Discount Amount</td>
                        <td>{{ $bundle->discount_amount }}</td>
                    </tr>

                    <tr>
                        <td>Payable Amount</td>
                        <td>{{ $bundle->payable_amount }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            @if ($bundle->status == 1)
                            <span class="badge bg-success px-3 py-2">Active</span>
                            @else
                            <span class="badge bg-danger px-3 py-2">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</td>
                    </tr>

                </tbody>
            </table>

            <h4 class="mt-2">Bundle Items List</h4>
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead class="">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Variant</th>
                            <th>Lid Option</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bundle->bundleItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->brand->name }}</td>
                            <td>{{ $item->productVariant->variant->pack_size ?? 'N/A' }}</td>
                            <td>{{ $item->productLidOption->lidOption->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection