@extends('adminPanel.layout.layout')

@section('css')
    <style>
        img {
            display: block;
            max-width: 100%;
        }

        .preview {
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }

        #image-preview {
            border: 1px dashed #ccc;
            padding: 10px;
            margin-top: 10px;
        }

        .image-wrapper {
            display: inline-block;
            position: relative;
        }

        .remove-image {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 10;
        }

        .variant-row {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 14px 10px;
            margin: 16px 0;
            background-color: #fcfcfd;
        }

        .variant-row-header {
            border-bottom: 1px dashed #ced4da;
            margin: 0 2px 8px;
            padding-bottom: 8px;
        }

        .variant-index-label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }

        .variant-size-row {
            border-top: 1px dashed #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        .variant-size-index-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.2px;
        }
    </style>
@endsection

@section('css_plugins')
    {{-- select2 --}}
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- summernote --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('main_content')
    <div class="page-content">
        <div class="card">
            <input type="hidden" id="selectimgdiv">
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('admin.update.product', $productInfo->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border-3 rounded border p-4">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label for="unit_type" class="form-label">Unit Type</label>
                                            <select name="unit_type" class="form-control" id="unit_type">
                                                <option value="">Select Unit Type</option>
                                                <option value="Weight" @selected($productInfo->unit_type == 'Weight')>Weight</option>
                                                <option value="PCs" @selected($productInfo->unit_type == 'PCs')>PCs</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="mb-3">
                                                <label for="serial_no" class="form-label">Serial No</label>
                                                <input type="text" class="form-control" name="serial_no" id="serial_no"
                                                    value="{{ $productInfo->serial_no }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputProductTitle" class="form-label">Product Name<strong
                                                        class="text-danger">*</strong> </label>
                                                <input type="text" name="name" class="form-control" id="productName"
                                                    value="{{ $productInfo->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputProductSlug" class="form-label">Product Slug<strong
                                                        class="text-danger"> *</strong> </label>
                                                <input type="text" class="form-control" name="slug"
                                                    id="inputProductSlug" placeholder="Enter product Slug"
                                                    value="{{ $productInfo->slug }}" required>
                                                <span id="productSlugValidateMessage"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label for="single-select-field" class="form-label">Product Category
                                                    <strong class="text-danger">*</strong> </label>
                                                <select name="category_id" class="form-select select2" id="category_id"
                                                    onchange="getSubcategory(this)" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($productCategory as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ $category->id == $productInfo->category_id ? 'selected' : '' }}>
                                                            {{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputProductType" class="form-label">Supplier</label>
                                            <select name="supplier_id" class="form-select select2" id="supplier_id">
                                                <option value="">Select Supplier</option>
                                                @foreach ($supplierList as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        {{ $supplier->id == $productInfo->supplier_id ? 'selected' : '' }}>
                                                        {{ $supplier->supplier_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label">SKU Code</label>
                                                <input type="text" name="sku_code" class="form-control"
                                                    value="{{ $productInfo->code }}" />

                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label">Brand</label>

                                                <select name="brand_id" class="form-select select2" id="brand_id">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brand as $b)
                                                        <option value="{{ $b->id }}"
                                                            {{ $b->id == $productInfo->brand_id ? 'selected' : '' }}>
                                                            {{ $b->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="inputProductType" class="form-label">Color</label>
                                            <select name="color" class="form-select select2" id="color">
                                                <option value="">Select Color</option>
                                                @foreach ($color as $c)
                                                    <option value="{{ $c->name }}"
                                                        {{ $c->name == $productInfo->color ? 'selected' : '' }}>
                                                        {{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-5">
                                            <label for="product_video_url" class="form-label">Product Video URL</label>
                                            <input type="text" name="product_video_url" class="form-control"
                                                id="product_video_url" value="{{ $productInfo->product_video_url }}">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" name="is_customizeable" type="checkbox"
                                                    value="1" id="is_customizeable"
                                                    {{ $productInfo->is_customizeable ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    Is Customizable
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-5" id="parentProductSelectCol">
                                            <label for="parentProduct" class="form-label">Parent Product</label>
                                            <select class="form-select select2" name="parent_product_id">
                                                <option value="">--- Select Parent Product ---</option>
                                                @foreach ($parentProducts as $parentProd)
                                                    <option value="{{ $parentProd->id }}" @selected($productInfo->parent_product_id == $parentProd->id)>
                                                        {{ $parentProd->name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- <div class="mb-3 col-md-6">
                                            <label for="image-input" class="form-label">Product Main Image</label>
                                            <input type="file" name="main_image" class="form-control" accept="image/*">
                                        </div> --}}

                                        {{-- <div class="mb-3 col-md-6">
                                            <label for="image-input" class="form-label">Product Images</label>
                                            <input type="file" name="images[]" class="form-control" id="image-input"
                                                accept="image/*" multiple>
                                        </div> --}}
                                    </div>
                                    <div class="row">

                                        <div class="mb-3 mt-4">
                                            <table class="table" id="product-images-table-x">
                                                <thead>
                                                    <tr>
                                                        <th>Preview</th>
                                                        <th>Upload Image</th>
                                                        <th>Alt Text</th>
                                                        <th>Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="product-image-body">
                                                    @foreach ($productInfo->productImage as $image)
                                                        <input type="hidden" name="image_id[]"
                                                            value="{{ $image->id }}">
                                                        <tr class="product-image-row">
                                                            <td>
                                                                <img src="{{ asset($image->image) }}"
                                                                    class="preview-image-x" width="80"
                                                                    height="80">
                                                            </td>
                                                            <td>
                                                                <input type="file" name="images[]"
                                                                    class="form-control image-input-x">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="img_alt[]"
                                                                    class="form-control" value="{{ $image->image_alt }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="img_name[]"
                                                                    class="form-control"
                                                                    value="{{ $image->image_name }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger remove-row-button-x"
                                                                    imageID="{{ $image->id }}">
                                                                    Remove
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    {{-- Main Image --}}
                                                    @if ($productInfo->image_path)
                                                        <tr class="product-image-row">
                                                            <td>
                                                                <img src="{{ asset($productInfo->image_path) }}"
                                                                    class="preview-image-x" width="80"
                                                                    height="80">
                                                            </td>
                                                            <td>
                                                                <input type="file" name="main_image"
                                                                    class="form-control image-input-x">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="main_img_alt"
                                                                    class="form-control"
                                                                    value="{{ $productInfo->image_alt }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="main_img_name"
                                                                    class="form-control"
                                                                    value="{{ $productInfo->image_name }}">
                                                            </td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                    @endif

                                                    <tr class="product-image-row-d-none d-none">
                                                        <td>
                                                            <img src="" class="preview-image-x" width="80"
                                                                height="80">
                                                        </td>
                                                        <td>
                                                            <input type="file" name="images[]"
                                                                class="form-control image-input-x">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="img_alt[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="img_name[]" class="form-control">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger remove-row-button-x">
                                                                Remove
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5">
                                                            <button type="button" class="btn btn-primary"
                                                                id="add-more-button-x">Add More</button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        {{-- <div class="col-md-12">
                                            <div id="image-preview" class="row">

                                                @if ($productInfo->image_path)
                                                    <div class="image-wrapper col-md-3">
                                                        <img src="{{ asset($productInfo->image_path) }}"
                                                            class="img-thumbnail"
                                                            style="max-width: 100%; margin-right: 10px;">
                                                        <button type="button" class="btn btn-danger remove-preview-image"
                                                            style="margin-top: 10px;">Remove</button>
                                                    </div>
                                                @endif


                                                @foreach ($productInfo->productImage as $image)
                                                    <div class="image-wrapper col-md-3">
                                                        <img src="{{ asset($image->image) }}" class="img-thumbnail"
                                                            style="max-width: 100%; margin-right: 10px;">
                                                        <button type="button"
                                                            class="btn btn-danger remove-existing-image"
                                                            style="margin-top: 10px;">Remove</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-12 mt-3">
                                            <div class="border-3 rounded border p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6 d-none">
                                                        <label for="inputCostPerPrice" class="form-label">Purchase
                                                            Cost</label>
                                                        <input type="number" step="any" name="current_purchase_cost"
                                                            class="form-control" id="current_purchase_cost"
                                                            value="{{ $productInfo->current_purchase_cost }}">
                                                    </div>
                                                    <div class="col-md-6 d-none">
                                                        <label for="inputPrice" class="form-label">Sell Price</label>
                                                        <input type="number" step="any" name="current_sale_price"
                                                            class="form-control" id="current_sale_price"
                                                            value="{{ $productInfo->current_sale_price }}">

                                                    </div>
                                                    <div class="col-md-6 d-none">
                                                        <label for="inputCompareatprice" class="form-label">Wholesale
                                                            Price</label>
                                                        <input type="number" step="any"
                                                            name="current_wholesale_price" class="form-control"
                                                            id="current_wholesale_price"
                                                            value="{{ $productInfo->current_wholesale_price }}">
                                                    </div>

                                                    <div class="col-md-6 d-none">
                                                        <label for="wholesale_minimum_qty" class="form-label">Wholesale
                                                            Qty </label>
                                                        <input type="number" step="any" name="wholesale_minimum_qty"
                                                            class="form-control" id="wholesale_minimum_qty"
                                                            value="{{ $productInfo->wholesale_minimum_qty }}">
                                                    </div>

                                                    <div class="col-md-6 d-none">
                                                        <label for="inputStarPoints" class="form-label">Available
                                                            Quantity </label>
                                                        <input type="number" step="any" name="available_quantity"
                                                            class="form-control" id="available_quantity"
                                                            value="{{ $productInfo->available_quantity }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputStarPoints" class="form-label">Discount Type
                                                        </label>
                                                        <select name="discount_type" class="form-select"
                                                            id="discount_type" onchange="discountType(this)">
                                                            <option value="0"
                                                                {{ $productInfo->discount_type == 0 ? 'selected' : '' }}>
                                                                Amount</option>
                                                            <option value="1"
                                                                {{ $productInfo->discount_type == 1 ? 'selected' : '' }}>
                                                                Percentage</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6" id="discount">
                                                        <label for="inputStarPoints" class="form-label">Discount
                                                            Amount</label>
                                                        <input type="number" step="any" name="discount"
                                                            class="form-control" id="discount"
                                                            value="{{ $productInfo->discount }}" placeholder="Discount">

                                                    </div>
                                                    <div class="col-md-6" id="stock_alert">
                                                        <label for="inputStarPoints" class="form-label">
                                                            Stock Alert
                                                        </label>
                                                        <input type="number" step="any" name="stock_alert"
                                                            class="form-control" value="{{ $productInfo->stock_alert }}">
                                                    </div>
                                                    <div class="col-md-6" id="order_limit">
                                                        <label for="inputStarPoints" class="form-label">
                                                            Order Limit
                                                        </label>
                                                        <input type="number" name="order_limit" class="form-control"
                                                            value="{{ $productInfo->order_limit }}">
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="is_trending"
                                                                type="checkbox" value="1"
                                                                {{ $productInfo->is_trending ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                Is Trending
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="is_popular"
                                                                type="checkbox" value="1"
                                                                {{ $productInfo->is_popular ? 'checked' : '' }}>
                                                            <label class="form-check-label">
                                                                Is Popular
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="col-md-12">
                                <!-- Variants Section -->
                                <div class="border-3 mt-4 rounded border p-4">
                                    <h4 class="card-title">Product Variants</h4>
                                    <div id="variantFields">
                                        @foreach ($productInfo->productVariants as $variant)
                                            <div class="row variant-row g-3 align-items-center"
                                                id="variant-{{ $variant->id }}"
                                                data-variant-index="{{ $loop->index }}">
                                                <div class="col-12 variant-row-header">
                                                    <span class="variant-index-label">Variant #{{ $loop->iteration }}</span>
                                                </div>

                                                <div class="col-sm-4">
                                                    <input type="number" name="variant_serial_no[]"
                                                        value="{{ $variant->serial_no }}" class="form-control"
                                                        placeholder="Serial No">
                                                </div>

                                                <div class="col-sm-4">

                                                    <select name="pack_size[]" class="form-select packSizeSelect"
                                                        id="pack_size_{{ $variant->id }}">
                                                        <option value="">Select Pack Size</option>
                                                        @foreach ($variants as $v)
                                                            <option value="{{ $v->id }}"
                                                                {{ $v->id == $variant->variant_id ? 'selected' : '' }}
                                                                data-pack-size="{{ $v->pack_size }}">
                                                                {{ $v->pack_size }} - ( {{ $v->name }} )
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-4">
                                                    <input type="number" step="any" name="price_per_peice[]"
                                                        class="form-control" id="price_per_peice_{{ $variant->id }}"
                                                        value="{{ $variant->price_per_peice }}">
                                                </div>

                                                <div class="col-sm-4">

                                                    <input type="number" step="any" name="price[]"
                                                        class="form-control" id="price_{{ $variant->id }}"
                                                        value="{{ $variant->price }}" readonly>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select name="v_brand_ids[]" class="form-select variant_brand_id"
                                                        id="v_brand_id_1">
                                                        <option value="">Select Brand Name</option>
                                                        @foreach ($brand as $brandone)
                                                            <option value="{{ $brandone->id }}"
                                                                {{ ixiSelected($variant->brand_id, $brandone->id) }}>
                                                                {{ $brandone->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select name="stock_status[]" class="form-select"
                                                        id="stock_status_1">
                                                        <option value="1"
                                                            {{ ixiSelected($variant->stock_status, 1) }}>In Stock</option>
                                                        <option value="0"
                                                            {{ ixiSelected($variant->stock_status, 0) }}>Out of Stock
                                                        </option>
                                                    </select>
                                                </div>
                                                <div
                                                    class="border-top border-bottom d-flex justify-content-between align-items-center m-2 py-2">
                                                    <h6>Variant Sizes</h6>
                                                    <button type="button"
                                                        class="btn btn-primary variantSizesRowAddBtn">+</button>
                                                </div>
                                                <div class="col-12 variantSizesContainer mb-3">
                                                    @foreach ($variant->variantSizes as $sizeIndex => $variantSize)
                                                        <div class="row g-2 align-items-end variant-size-row">
                                                            <div class="col-12">
                                                                <span
                                                                    class="variant-size-index-label">V{{ $loop->parent->iteration }}
                                                                    > S{{ $loop->iteration }}</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Size</label>
                                                                <select class="form-select"
                                                                    name="variant_sizes[{{ $loop->parent->index }}][{{ $sizeIndex }}][size_id]">
                                                                    <option value="">Select Size</option>
                                                                    @foreach ($sizes as $size)
                                                                        <option value="{{ $size->id }}"
                                                                            @selected($size->id == $variantSize->size_id)>
                                                                            {{ $size->size }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label class="form-label">Description</label>
                                                                <input type="text" class="form-control"
                                                                    name="variant_sizes[{{ $loop->parent->index }}][{{ $sizeIndex }}][description]"
                                                                    value="{{ $variantSize->description }}">
                                                            </div>

                                                            <div class="col-md-2 d-grid">
                                                                <button type="button"
                                                                    class="btn btn-danger variantSizesRowRemoveBtn">-</button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-danger remove-variant"
                                                        data-id="{{ $variant->id }}">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-primary" id="addVariant">Add More
                                            Variant</button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addNewPackSizeModal">
                                            <i class="lni lni-circle-plus"></i>
                                            Add Variant
                                        </button>
                                    </div>
                                </div>
                                <!-- Options Section -->
                                {{-- <div class="border-3 mt-4 rounded border p-4">
                                    <h4 class="card-title">Product Color Option</h4>
                                    <div id="optionFields">
                                        @foreach ($productInfo->productOptions as $option)
                                            <div class="row option-row mb-2" id="option-{{ $option->id }}">
                                                <div class="col-sm-4">
                                                    <label for="size_{{ $option->id }}" class="form-label">Size</label>
                                                    <select name="size[]" class="form-select"
                                                        id="size_{{ $option->id }}">
                                                        <option value="">Select Size</option>
                                                        @foreach ($sizes as $size)
                                                            <option value="{{ $size->id }}"
                                                                {{ $size->id == $option->size_id ? 'selected' : '' }}>
                                                                {{ $size->size }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="size_{{ $option->id }}"
                                                        class="form-label">Option</label>
                                                    <select name="option[]" class="form-select"
                                                        id="option_{{ $option->id }}">
                                                        <option value="">Select option</option>
                                                        @foreach ($coloroptions as $coloroption)
                                                            <option value="{{ $coloroption->id }}"
                                                                {{ $coloroption->id == $option->option_id ? 'selected' : '' }}>
                                                                {{ $coloroption->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="options_price_{{ $option->id }}"
                                                        class="form-label">Options Price</label>
                                                    <input type="number" step="any" name="options_price[]"
                                                        class="form-control" id="options_price_{{ $option->id }}"
                                                        value="{{ $option->options_price }}">
                                                </div>
                                                <div class="col-sm-4">
                                                    <button type="button" class="btn btn-danger remove-option"
                                                        data-id="{{ $option->id }}">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mb-2 text-end">
                                        <button type="button" class="btn btn-primary" id="addSizeColorBtn">Add
                                            More</button>
                                    </div>
                                </div> --}}

                                <!-- Product Lid Options Section Starts -->

                                <div class="border-3 mt-4 rounded border p-4">
                                    <h4 class="card-title">Product Lid Options</h4>
                                    <div id="productLidOptionsContainer">

                                        @foreach ($productLidOptions as $productLidOption)
                                            <div class="row productLidOptionRow mb-2"
                                                id="productLidOptionRow-{{ $productLidOption->id }}">
                                                <div class="col-sm-3">
                                                    <input type="hidden" name="oldProductLidOptionIDs[]"
                                                        value="{{ $productLidOption->id }}">

                                                    <label for="productLidOption-{{ $productLidOption->id }}"
                                                        class="form-label">
                                                        Product Lid Option
                                                    </label>
                                                    <select name="productLidOption[]" class="form-select"
                                                        id="productLidOption-{{ $productLidOption->id }}">
                                                        <option value="">Select Lid Option for Product</option>
                                                        @foreach ($lidOptions as $lidOption)
                                                            <option value="{{ $lidOption->id }}"
                                                                {{ $productLidOption->lid_option_id == $lidOption->id ? 'selected' : '' }}>
                                                                {{ $lidOption->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="productLidOptionPrice-{{ $productLidOption->id }}"
                                                        class="form-label">
                                                        Price
                                                    </label>
                                                    <input type="number" step="any" name="productLidOptionPrice[]"
                                                        class="form-control"
                                                        id="productLidOptionPrice-{{ $productLidOption->id }}"
                                                        placeholder="Product Lid Option Price"
                                                        value="{{ $productLidOption->price }}">
                                                </div>

                                                <div class="col-sm-3 d-flex align-items-end">
                                                    <button type="button"
                                                        class="btn btn-danger removeProductLidOptionRow"
                                                        onclick="removeProductLidOptionRow('{{ $productLidOption->id }}')">
                                                        <i class="lni lni-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    <div class="mb-2 text-end">
                                        <button type="button" class="btn btn-primary" id="addProductLidOptionNewRow">Add
                                            More</button>
                                    </div>
                                </div>

                                <!-- Product Lid Options Section End -->

                                <!-- Product Packaging Options Section Starts -->
                                <div class="border-3 mt-4 rounded border p-4" id="productPackagingOptionsSection">
                                    <h4 class="card-title">Product Packaging Options</h4>
                                    <div id="productPackagingOptionsContainer">
                                        @foreach ($productInfo->packagingOptions as $pkgOption)
                                            <div class="row productPackagingOptionRow mb-4"
                                                id="productPackagingOptionRow-{{ $pkgOption->id }}">
                                                <div class="col-sm-3">
                                                    <label for="productPackagingPrintLocation-{{ $pkgOption->id }}"
                                                        class="form-label">
                                                        Print Location
                                                    </label>
                                                    <select
                                                        name="productPackagingOptions[{{ $pkgOption->id }}][print_location]"
                                                        class="form-select"
                                                        id="productPackagingPrintLocation-{{ $pkgOption->id }}">
                                                        <option value="">Select Print Location</option>
                                                        <option value="lid" @selected($pkgOption->print_location == 'lid')>Lid</option>
                                                        <option value="side" @selected($pkgOption->print_location == 'side')>Side</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="productPackagingSideOption-{{ $pkgOption->id }}"
                                                        class="form-label">
                                                        Side Option
                                                    </label>
                                                    <select
                                                        name="productPackagingOptions[{{ $pkgOption->id }}][side_option]"
                                                        class="form-select"
                                                        id="productPackagingSideOption-{{ $pkgOption->id }}">
                                                        <option value="">Select Side</option>
                                                        <option value="front" @selected($pkgOption->side_option == 'front')>Front</option>
                                                        <option value="back" @selected($pkgOption->side_option == 'back')>Back</option>
                                                        <option value="left" @selected($pkgOption->side_option == 'left')>Left</option>
                                                        <option value="right" @selected($pkgOption->side_option == 'right')>Right</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="productPackagingPrice-{{ $pkgOption->id }}"
                                                        class="form-label">
                                                        Price
                                                    </label>
                                                    <input type="number" step="any"
                                                        name="productPackagingOptions[{{ $pkgOption->id }}][price]"
                                                        class="form-control" value="{{ $pkgOption->price }}"
                                                        id="productPackagingPrice-{{ $pkgOption->id }}"
                                                        placeholder="Product Packaging Option Price">
                                                </div>
                                                <div class="col-sm-3 d-flex align-items-end">
                                                    <button type="button"
                                                        class="btn btn-danger productPackagingRemoveRowBtn">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mb-2 text-end">
                                        <button type="button" class="btn btn-primary"
                                            id="addProductPackagingOptionNewRow">Add More</button>
                                    </div>
                                </div>
                                <!-- Product Packaging Options Section Ends -->
                            </div>

                            <div class="border-3 mt-2 rounded border p-4">
                                <div class="mb-3">
                                    <label for="inputProductDescription" class="form-label">Description</label>
                                    <textarea name="description" id="inputProductDescription" class="form-control">{!! $productInfo->description !!}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="inputAdditionalInformation" class="form-label">Additional
                                        Information</label>
                                    <textarea name="additional_information" id="inputAdditionalInformation" class="form-control">{!! $productInfo->additional_information !!}</textarea>
                                </div>
                            </div>



                            @include('adminPanel.partials.seo_form_fields._edit', [
                                'seoMetadata' => $productSeoMetadata,
                            ])

                            <div class="col-12 mt-3">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Save Product</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal for adding new variant --}}

    <div class="modal fade" id="addNewPackSizeModal" tabindex="-1" aria-labelledby="addNewPackSizeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewPackSizeModalLabel">Create Product Variant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <label for="nameInput" class="col-sm-12 col-form-label pr-0">
                            Name
                        </label>
                        <div class="col-sm-12">
                            <input type="number" id="nameInput" name="name" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="inputname" class="col-sm-12 col-form-label pr-0">
                            Pack Size
                        </label>
                        <div class="col-sm-12">
                            <input type="number" id="pakSizeInput" name="pack_size" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end p-3">
                    <button type="button" class="btn btn-primary" onclick="saveNewPackSize()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <template id="variantSizesRow">
        <div class="row g-2 align-items-end variant-size-row">
            <div class="col-12">
                <span class="variant-size-index-label">V1 > S1</span>
            </div>

            <div class="col-md-4">
                <label class="form-label">Size</label>
                <select class="form-select" name="variant_sizes[_VINDEX_][_SINDEX_][size_id]">
                    <option value="">Select Size</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}">{{ $size->size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Description</label>
                <input type="text" class="form-control" name="variant_sizes[_VINDEX_][_SINDEX_][description]">
            </div>

            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-danger variantSizesRowRemoveBtn">-</button>
            </div>

        </div>
    </template>
@endsection

@section('js_plugins')
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Summernote --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 and summernote
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            $('#inputProductDescription').summernote({
                height: 200
            });

            $('#inputAdditionalInformation').summernote({
                height: 200
            });

            // Add a new variant size row
            $(document).on('click', '.variantSizesRowAddBtn', function() {
                const variantRow = $(this).closest('.variant-row');
                const container = variantRow.find('.variantSizesContainer');

                const variantIndex = variantRow.data('variant-index');
                const sizeIndex = container.find('.variant-size-row').length;

                let newRow = $('#variantSizesRow').html();

                newRow = newRow
                    .replace(/_VINDEX_/g, variantIndex)
                    .replace(/_SINDEX_/g, sizeIndex);

                container.append(newRow);
                updateVariantLabels();
            });

            // Remove a variant size row
            $(document).on('click', '.variantSizesRowRemoveBtn', function() {
                const container = $(this).closest('.variantSizesContainer');
                $(this).closest('.variant-size-row').remove();

                // Reindex remaining rows
                container.find('.variant-size-row').each(function(newIndex) {
                    $(this).find('select, input').each(function() {
                        let name = $(this).attr('name');
                        name = name.replace(/\[\d+\]$/, `[${newIndex}]`);
                        $(this).attr('name', name);
                    });
                });
                updateVariantLabels();
            });


            // Add new variant functionality
            let variantIndexCounter = $('#variantFields .variant-row').length;
            $('#addVariant').click(function() {
                let variantId = new Date().getTime(); // Use timestamp for unique ID
                let variantIndex = variantIndexCounter;
                variantIndexCounter++;
                let html = `
            <div class="row variant-row g-3 align-items-center" id="variant-${variantId}" data-variant-index="${variantIndex}">
                <div class="col-12 variant-row-header">
                    <span class="variant-index-label">Variant #${variantIndex + 1}</span>
                </div>
                 <div class="col-sm-4">
                    <input type="number" name="variant_serial_no[]" class="form-control" placeholder="Serial No">
                </div>
                <div class="col-sm-4">

                    <select name="pack_size[]" class="form-select packSizeSelect" id="pack_size_${variantId}">
                        <option value="">Select Pack Size</option>
                        @foreach ($variants as $v)
                            <option data-pack-size="{{ $v->pack_size }}" value="{{ $v->id }}">
                                {{ $v->pack_size }} - ( {{ $v->name }} )
                                 </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-4">
                    <input type="number" step="any" name="price_per_peice[]" class="form-control" id="price_per_peice_${variantId}" placeholder="Price Per Peice">
                </div>

                <div class="col-sm-4">
                    <input type="number" step="any" name="price[]" class="form-control" id="price_${variantId}" readonly>
                </div>
                <div class="col-sm-4">
                <select name="v_brand_ids[]" class="form-select variant_brand_id" id="v_brand_id_${variantId}">
                    <option value="">Select Brand Name</option>
                    @foreach ($brand as $brandone)
                        <option value="{{ $brandone->id }}">{{ $brandone->name }}</option>
                    @endforeach
                </select>
            </div>
             <div class="col-sm-4">
                    <select name="stock_status[]" class="form-select" id="stock_status_1">
                        <option value="1">In Stock</option>
                       <option value="0">Out of Stock</option>
                     </select>
              </div>
               <div class="border-top border-bottom d-flex justify-content-between align-items-center m-2 py-2">
                    <h6>Variant Sizes</h6>
                    <button type="button" class="btn btn-primary variantSizesRowAddBtn">+</button>
                </div>
                <div class="col-12 variantSizesContainer mb-3">
                    {{-- Dynamically using JS --}}
                </div>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-danger remove-variant" data-id="${variantId}">Remove</button>
                </div>
            </div>
        `;
                const newVariantRow = $(html);
                $('#variantFields').append(newVariantRow);

                if (latestPackSizeOption) {
                    const newSelect = newVariantRow.find(`#pack_size_${variantId}`);
                    newSelect.find('option:first').after(latestPackSizeOption.clone());
                }

                updateVariantLabels();
            });

            // Remove variant functionality
            $(document).on('click', '.remove-variant', function() {
                const variantId = $(this).data('id');
                $(`#variant-${variantId}`).remove();
                updateVariantLabels();
            });

            function updateVariantLabels() {
                $('#variantFields .variant-row').each(function(index) {
                    $(this).find('.variant-index-label').text(`Variant #${index + 1}`);
                    $(this).find('.variant-size-row').each(function(sizeIndex) {
                        $(this).find('.variant-size-index-label').text(
                            `V${index + 1} > S${sizeIndex + 1}`
                        );
                    });
                });
            }

            updateVariantLabels();

            // Add new size and color functionality
            $('#addSizeColorBtn').click(function() {
                let optionId = new Date().getTime(); // Use timestamp for unique ID
                let html = `
        <div class="row mb-2 option-row" id="option-${optionId}">
            <div class="col-sm-4">
                <label for="size_${optionId}" class="form-label">Size</label>
                <select name="size[]" class="form-select" id="size_${optionId}">
                    <option value="">Select Size</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}">{{ $size->size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label for="option_${optionId}" class="form-label">Option</label>
                <select name="option[]" class="form-select" id="option_${optionId}">
                    <option value="">Select option</option>
                    @foreach ($coloroptions as $coloroption)
                        <option value="{{ $coloroption->id }}">{{ $coloroption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label for="options_price_${optionId}" class="form-label">Options Price</label>
                <input type="number" step="any" name="options_price[]" class="form-control" id="options_price_${optionId}">
            </div>
            <div class="col-sm-4">
                <button type="button" class="btn btn-danger remove-option" data-id="${optionId}">Remove</button>
            </div>
        </div>
    `;
                $('#optionFields').append(html);
            });

            // Remove option functionality
            $(document).on('click', '.remove-option', function() {
                const optionId = $(this).data('id');
                $(`#option-${optionId}`).remove();
            });


            // Display existing images and primary image
            const existingImages = @json($productInfo->productImage);

            const primaryImage = '{{ asset($productInfo->image_path) }}'; // Get primary image path

            displayExistingImagesAndPrimary();

            function displayExistingImagesAndPrimary() {
                $('#image-preview').empty(); // Clear any existing content

                // Display the primary image without the "Remove" button
                if ('{{ $productInfo->image_path }}') {
                    const imgWrapper = $('<div>').addClass('image-wrapper col-md-3');
                    const img = $('<img>').attr('src', primaryImage).addClass('img-thumbnail').css({
                        'max-width': '100%',
                        'margin-right': '10px'
                    });
                    const removeMainImg = $('<button>')
                        .addClass('btn btn-danger btn-sm remove-btn')
                        .text('X')
                        .css({
                            position: 'absolute',
                            top: '5px',
                            right: '14px'
                        })
                        .on('click', function() {
                            imgWrapper.remove();
                            removeImageFromServer("main", "{{ $productInfo->id }}");
                        });
                    imgWrapper.append(img).append(removeMainImg);
                    $('#image-preview').append(imgWrapper);
                }

                // Display additional product images with "Remove" buttons
                existingImages.forEach((image, index) => {

                    const imgWrapper = $('<div>').addClass('image-wrapper col-md-3').css('position',
                        'relative');
                    const img = $('<img>').attr('src', `{{ asset('${image.image}') }}`).addClass(
                            'img-thumbnail')
                        .css({
                            'max-width': '100%',
                            'margin-right': '10px'
                        });
                    const removeButton = $('<button>')
                        .addClass('btn btn-danger btn-sm remove-btn')
                        .text('X')
                        .css({
                            position: 'absolute',
                            top: '5px',
                            right: '14px'
                        })
                        .on('click', function() {
                            imgWrapper.remove();
                            removeImageFromServer(image.id);
                        });

                    imgWrapper.append(img).append(removeButton);
                    $('#image-preview').append(imgWrapper);
                });
            }




            $('#image-input').on('change', function() {
                const files = this.files;
                const previewContainer = $('#image-preview');

                // Clear all existing images (including primary) when new images are added
                previewContainer.empty();

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const imgWrapper = $('<div>').addClass('image-wrapper col-md-3');
                            const img = $('<img>').attr('src', e.target.result).addClass(
                                'img-thumbnail').css({
                                'max-width': '100%',
                                'margin-right': '10px'
                            });

                            // Add a "Remove" button for newly added images
                            const removeButton = $('<button>')
                                .text('Remove')
                                .addClass('btn btn-danger remove-preview-image')
                                .css({
                                    marginTop: '10px'
                                })
                                .on('click', function() {
                                    imgWrapper.remove(); // Remove image preview
                                    removeFileFromInput(
                                        file); // Remove this image from the input field
                                });

                            imgWrapper.append(img).append(removeButton);
                            previewContainer.append(imgWrapper);
                        };

                        reader.readAsDataURL(file);
                    }
                }
            });

            // Function to remove a file from input field
            function removeFileFromInput(fileToRemove) {
                const inputField = $('#image-input')[0];
                const dataTransfer = new DataTransfer();

                for (let i = 0; i < inputField.files.length; i++) {
                    const file = inputField.files[i];
                    if (file !== fileToRemove) {
                        dataTransfer.items.add(file); // Add remaining files
                    }
                }

                inputField.files = dataTransfer.files; // Update input field with remaining files
            }

            // Remove preview image for new images
            $(document).on('click', '.remove-preview-image', function() {
                $(this).parent().remove(); // Removes the preview image wrapper
            });
        });


        $('#inputProductSlug').on('keyup', function() {

            var productId = '{{ $productInfo->id }}';
            var product_slug = $(this).val();
            var url_link = "{{ route('product.slug.validate') }}";
            $.ajax({
                url: url_link,
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    productId: productId,
                    slug: product_slug,
                },
                success: function(response) {

                    const messageElement = $('#productSlugValidateMessage');
                    if (response.success) {
                        messageElement.removeClass('text-danger').addClass('text-success').text(response
                            .success);
                    } else if (response.error) {
                        messageElement.removeClass('text-success').addClass('text-danger').text(response
                            .error);
                    } else {
                        messageElement.text('');
                    }

                },
                error: function(xhr) {
                    console.log("AJAX Error", xhr);
                }
            });
        });


        $(document).on('click', '.dropdown-toggle', function(ev) {
            $(this).parent().find('.dropdown-menu').show();
        });

        $(document).on('click', '.note-editable.card-block', function() {
            $('.note-dropdown-menu.dropdown-menu').hide();
        });

        var newLidOptionRowID = '{{ $productLidOption->id ?? 0 }}';
        $(document).on('click', '#addProductLidOptionNewRow', function() {

            newLidOptionRowID++;
            var newLidOptionRow = `
                    <div class="row mb-2 productLidOptionRow" id="productLidOptionRow-${newLidOptionRowID}">
                        <div class="col-sm-3">
                            <input type="hidden" name="oldProductLidOptionIDs[]" value="0">
                            <label for="productLidOption-${newLidOptionRowID}" class="form-label">
                                Product Lid Option
                            </label>
                            <select name="productLidOption[]" class="form-select" id="productLidOption-${newLidOptionRowID}">
                                <option value="">Select Lid Option for Product</option>
                                @foreach ($lidOptions as $lidOption)
                                <option value="{{ $lidOption->id }}">
                                    {{ $lidOption->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="productLidOptionPrice-${newLidOptionRowID}" class="form-label">
                                Price
                            </label>
                            <input type="number" step="any" name="productLidOptionPrice[]" class="form-control" id="productLidOptionPrice-${newLidOptionRowID}" placeholder="Product Lid Option Price">
                        </div>



                        <div class="col-sm-3 d-flex align-items-end">
                            <button type="button" class="btn btn-danger removeProductLidOptionRow" onclick="removeProductLidOptionRow(${newLidOptionRowID})">
                            <i class="lni lni-trash"></i>
                            </button>
                        </div>
                    </div>
        `;

            $('#productLidOptionsContainer').append(newLidOptionRow);

        });

        function removeProductLidOptionRow(id) {
            let lidOptionRow = $('#productLidOptionRow-' + id);
            if (lidOptionRow) {
                lidOptionRow.remove();
            }
        }



        $(document).on('input', '[id^="price_per_peice_"]', function() {
            var id = $(this).attr('id');
            var index = id.split('_')[3];

            var pricePerPiece = parseFloat($('#' + id).val());
            var packSize = parseFloat($('#pack_size_' + index).find('option:selected').data('pack-size'));

            if (pricePerPiece && packSize) {
                var price = pricePerPiece * packSize;
                $('#price_' + index).val(price.toFixed(2));
            } else {
                $('#price_' + index).val('');
            }
        });



        document.getElementById('add-more-button-x').addEventListener('click', function() {
            var table = document.getElementById('product-images-table-x');
            var newRow = table.querySelector('.product-image-row-d-none').cloneNode(true);
            newRow.classList.remove('d-none');
            newRow.querySelector('.preview-image-x').src = '';
            newRow.querySelector('.image-input-x').value = '';
            newRow.querySelector('input[name="img_alt[]"]').value = '';
            newRow.querySelector('input[name="img_name[]"]').value = '';

            newRow.querySelector('.remove-row-button-x').removeAttribute('imageID');
            console.log(newRow.querySelector('.remove-row-button-x'));

            var tbody = table.querySelector('.product-image-body');
            tbody.insertBefore(newRow, tbody.lastElementChild);
        });

        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('image-input-x')) {
                var input = event.target;
                var reader = new FileReader();
                reader.onload = function(e) {
                    input.closest('tr').querySelector('.preview-image-x').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-row-button-x')) {
                var row = event.target.closest('tr');
                var imageID = event.target.getAttribute('imageID');
                if (imageID) {
                    removeImageFromServer(imageID);
                }
                row.parentNode.removeChild(row);
            }
        });

        function removeImageFromServer(imgID, pID = null) {
            $.ajax({
                url: 'remove-product-image',
                method: 'POST',
                data: {
                    imgID: imgID,
                    pID: pID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Image removed:', response);
                },
                error: function(error) {
                    console.error('Error removing image:', error);
                }
            });
        }


        let latestPackSizeOption = null;

        function saveNewPackSize() {
            let name = $('#nameInput').val();
            let packSize = $('#pakSizeInput').val();

            $.ajax({
                url: "{{ route('admin.product.saveNewPackSize') }}",
                type: 'POST',
                data: {
                    name: name,
                    pack_size: packSize,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Create new option element
                    latestPackSizeOption = $('<option>', {
                        value: response.id,
                        text: response.pack_size + ' - (' + response.name + ')',
                        'data-pack-size': response.pack_size
                    });

                    // Append to all existing select boxes
                    $('.packSizeSelect').each(function() {
                        $(this).find('option:first').after(latestPackSizeOption.clone());
                    });

                    $('#pakSizeInput').val('');
                    $('#addNewPackSizeModal').modal('hide');
                },
                error: function(xhr) {
                    if (xhr.status === 409) {
                        alert('This pack size already exists!');
                    } else {
                        alert('Error adding variant.');
                    }
                }
            });
        }


        // Product Packaging Options Section Starts

        let packagingRowCount = Number('{{ $pkgOption->id ?? 0 }}') + 1;


        // Add new row
        $("#addProductPackagingOptionNewRow").on("click", function() {
            let newRow = `
        <div class="row mb-4 productPackagingOptionRow" id="productPackagingOptionRow-${packagingRowCount}">
            <div class="col-sm-3">
                <label for="productPackagingPrintLocation-${packagingRowCount}" class="form-label">
                    Print Location
                </label>
                <select name="productPackagingOptions[${packagingRowCount}][print_location]" class="form-select"
                        id="productPackagingPrintLocation-${packagingRowCount}">
                    <option value="">Select Print Location</option>
                    <option value="lid">Lid</option>
                    <option value="side">Side</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label for="productPackagingSideOption-${packagingRowCount}" class="form-label">
                    Side Option
                </label>
                <select name="productPackagingOptions[${packagingRowCount}][side_option]" class="form-select"
                        id="productPackagingSideOption-${packagingRowCount}">
                    <option value="">Select Side</option>
                    <option value="front">Front</option>
                    <option value="back">Back</option>
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label for="productPackagingPrice-${packagingRowCount}" class="form-label">
                    Price
                </label>
                <input type="number" step="any" name="productPackagingOptions[${packagingRowCount}][price]"
                       class="form-control" id="productPackagingPrice-${packagingRowCount}"
                       placeholder="Product Packaging Option Price">
            </div>
            <div class="col-sm-3 d-flex align-items-end">
                <button type="button" class="btn btn-danger productPackagingRemoveRowBtn">Remove</button>
            </div>
        </div>
        `;

            $("#productPackagingOptionsContainer").append(newRow);
            packagingRowCount++;
        });


        $(document).on('change', 'select[id^="productPackagingPrintLocation"]', function() {
            var rowId = $(this).attr('id').split('-')[1];
            console.log("Row ID", rowId);
            var $sideSelect = $('#productPackagingSideOption-' + rowId);

            // Clear existing options
            $sideSelect.empty();

            // Add default option
            $sideSelect.append('<option value="">Select Side</option>');

            if ($(this).val() === 'lid') {
                // Add Top / Bottom options
                $sideSelect.append('<option value="front" selected>Front</option>');
            } else if ($(this).val() === 'side') {
                // Add Front / Back / Left / Right options
                $sideSelect.append('<option value="front">Front</option>');
                $sideSelect.append('<option value="back">Back</option>');
                $sideSelect.append('<option value="left">Left</option>');
                $sideSelect.append('<option value="right">Right</option>');
            }
        });

        // Remove row
        $(document).on("click", ".productPackagingRemoveRowBtn", function() {
            $(this).closest(".productPackagingOptionRow").remove();
        });

        function togglePackagingSection() {
            if ($('#is_customizeable').is(':checked')) {
                $('#productPackagingOptionsSection').show();
                $('#parentProductSelectCol').show();
            } else {
                $('#productPackagingOptionsSection').hide();
                $('#parentProductSelectCol').hide();
            }
        }

        // Run on page load
        togglePackagingSection();

        // Run whenever the checkbox is clicked
        $('#is_customizeable').change(function() {
            togglePackagingSection();
        });
    </script>
@endsection
