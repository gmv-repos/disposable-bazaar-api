@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                Sorting Of Categories
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.product.category.sort.orders.store') }}">
                    @csrf

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Header Dropdown</th>
                                <th>One Stop Shop</th>
                                <th>Products Slider Top</th>
                            </tr>
                        </thead>

                        <tbody>
                            @include('adminPanel.product_category._category_sort_row', [
                                'categories' => $categories,
                                'level' => 1,
                            ])
                        </tbody>
                    </table>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            Save Sort Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
