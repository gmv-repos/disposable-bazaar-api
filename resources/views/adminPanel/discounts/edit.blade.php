@extends('adminPanel.layout.layout')

@section('css')
    <style>
        /* Add any additional custom styles here */
    </style>
@endsection

@section('main_content')
    <div class="page-content">

        <div class="card">
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- For PUT method -->

                        <div class="row">
                            <!-- Discount Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Discount Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Enter discount name" value="{{ old('name', $discount->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <!-- Discount Percentage -->
                            <div class="col-md-6 mb-3">
                                <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                                <input type="number" name="discount_percentage" id="discount_percentage"
                                    class="form-control" step="0.01" min="0" max="100"
                                    value="{{ old('discount_percentage', $discount->discount_percentage) }}" required>
                                @error('discount_percentage')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label for="category_ids" class="form-label">Category</label>
                                <select name="category_ids[]" id="category_ids" class="form-control" multiple required>
                                    <option value="allCategories" {{ in_array('allCategories', old('category_ids', $selectedCategoryIds)) ? 'selected' : '' }}>
                                        All Categories
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $selectedCategoryIds)) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_ids')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <!-- Product Variant -->
                            <div class="col-md-6 mb-3">
                                <label for="product_ids" class="form-label">Product</label>
                                <select name="product_ids[]" id="product_ids" class="form-control" required multiple>
                                    <option value="allProducts" {{ in_array('allProducts', old('product_ids', $selectedProductIds)) ? 'selected' : '' }}>
                                        All Products
                                    </option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', $selectedProductIds)) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_ids')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <!-- Start Time -->
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($discount->start_time)->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('start_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- End Time -->
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($discount->end_time)->format('Y-m-d\TH:i')) }}"
                                    required>
                                @error('end_time')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">Update Discount</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('css_plugins')
    {{-- select2--}}
    <link rel="stylesheet"
        href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- select2--}}
    {{-- crop--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
    {{-- crop--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('js_plugins')
    {{--select 2--}}
    <script
        src="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/input-tags/js/tagsinput.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/select2/js/select2-custom.js"></script>
    {{--select 2--}}

    {{-- crop--}}
@endsection

@section('js')

<script>
    $(document).ready(function () {
        // Initialize select2 on category and product fields
        $('#category_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select categories',
            closeOnSelect: false,
            allowClear: true
        
        });

        $('#product_ids').select2({
            theme: 'bootstrap-5', 
            placeholder: 'Select products',
            closeOnSelect: false,
            allowClear: true
        });

        // Handle category change event
        $('#category_ids').on('change', function () {
            const selectedCategories = $(this).val();

            // If "All Categories" is selected, select all categories
            if (selectedCategories.includes('allCategories')) {
                $('#category_ids').val(
                    $('#category_ids option:not([value="allCategories"])').map(function () {
                        return $(this).val();
                    }).get()
                ).trigger('change');
                return;
            }

            // Fetch products based on selected categories
            $.ajax({
                url: '{{ route('discounts.fetch-products') }}',
                type: 'POST',
                data: {
                    category_ids: selectedCategories,
                    _token: '{{ csrf_token() }}'
                },
                success: function (products) {
                    // Clear existing product options and add "All Products"
                    $('#product_ids').empty();
                    $('#product_ids').append('<option value="allProducts">All Products</option>');

                    // Add products returned from the AJAX response
                    products.forEach(function (product) {
                        $('#product_ids').append(
                            `<option value="${product.id}" selected>${product.name}</option>`
                        );
                    });

                    // Trigger change to update the select2 view
                    $('#product_ids').trigger('change');
                }
            });
        });

        // Handle "All Products" selection
        $('#product_ids').on('select2:select', function (e) {
            if (e.params.data.id === 'allProducts') {
                // Select all product options (except "All Products")
                var allOptions = $('#product_ids option:not([value="allProducts"])').map(function () {
                    return $(this).val();
                }).get();
                $('#product_ids').val(allOptions).trigger('change');
            }
        });

        // Handle unselecting "All Products"
        $('#product_ids').on('select2:unselect', function (e) {
            if (e.params.data.id === 'allProducts') {
                $('#product_ids').val(null).trigger('change');
            }
        });
    });
</script>
@endsection