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

@section('main_content')
    <div class="page-content">
        <div class="card">
            <input type="hidden" id="selectimgdiv">
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('admin.store.product') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border-3 rounded border p-4">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label for="unit_type" class="form-label">Unit Type</label>
                                            <select name="unit_type" class="form-control" id="unit_type">
                                                <option value="">Select Unit Type</option>
                                                <option value="Weight">Weight</option>
                                                <option value="PCs">PCs</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="mb-3">
                                                <label for="serial_no" class="form-label">Serial No</label>
                                                <input type="text" class="form-control" name="serial_no" id="serial_no"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputProductTitle" class="form-label">Product Name<strong
                                                        class="text-danger">*</strong> </label>
                                                <input type="text" class="form-control" name="name"
                                                    id="inputProductTitle" placeholder="Enter product Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputProductTitle" class="form-label">No of Piece Qty in
                                                    Carton<strong class="text-danger">*</strong> </label>
                                                <input type="number" step="any" class="form-control"
                                                    name="no_of_piece_qty_in_carton" id="inputProductTitle"
                                                    placeholder="Enter No of Piece Qty in Carton" value="0" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="inputProductSlug" class="form-label">Product Slug<strong
                                                        class="text-danger"> *</strong> </label>
                                                <input type="text" class="form-control" name="slug"
                                                    id="inputProductSlug" placeholder="Enter product Slug" required>
                                                <span id="productSlugValidateMessage"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-2">
                                                <label for="single-select-field" class="form-label">Product Category
                                                    <strong class="text-danger">*</strong> </label>
                                                <select class="form-select" name="category_id" id="single-select-field"
                                                    data-placeholder="Choose Category" required>
                                                    <option></option>
                                                    @foreach ($productCategory as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputProductType" class="form-label">Supplier</label>
                                            <select name="supplier_id" class="form-select select2" id="inputProductType"
                                                data-placeholder="Choose supplier">
                                                <option></option>
                                                @foreach ($supplierList as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label">SKU Code</label>
                                                <input type="text" name="sku_code" class="form-control" />

                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label">Brand</label>
                                                <select
                                                    class="js-example-basic-multiple form-control form-control-color w-100"
                                                    name="brand_id" id="brandSelect">
                                                    <option value="">No Brand</option>
                                                    @foreach ($brand as $dataBrand)
                                                        <option value="{{ $dataBrand->id }}">{{ $dataBrand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <label for="inputProductType" class="form-label">Color</label>
                                            <select class="js-example-basic-multiple form-control form-control-color w-100"
                                                name="color[]" multiple="multiple">

                                                @foreach ($color as $dataColor)
                                                    <option value="{{ $dataColor->name }}">{{ $dataColor->name }} </option>
                                                @endforeach

                                            </select>
                                        </div>


                                        <div class="col-sm-5">
                                            <label for="inputProductVideoUrl" class="form-label">Product Video Url</label>
                                            <input type="text" name="product_video_url" id="product_video_url"
                                                class="form-control" />
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" name="is_customizeable" type="checkbox"
                                                    value="1" id="is_customizeable">
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
                                                    <option value="{{ $parentProd->id }}">{{ $parentProd->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                    </div>

                                    <div class="mb-3 mt-4">
                                        <table class="table" id="product-images-table-x">
                                            <thead>
                                                <tr>
                                                    <th>Preview</th>
                                                    <th>Upload Image</th>
                                                    <th>Alt</th>
                                                    <th>Name</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="product-image-body">
                                                <tr class="product-image-row">
                                                    <td>
                                                        <img class="preview-image-x" width="80" height="80">
                                                    </td>
                                                    <td>
                                                        <input type="file" name="product_img[]"
                                                            class="form-control image-input-x">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="product_img_alt[]"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="product_img_name[]"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-danger remove-row-button-x d-none">Remove</button>
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
                                        {{-- Image Upload Section --}}
                                        {{-- <div class="form-group"> --}}
                                        {{-- <label for="product_img" class="form-label">Product Images</label> --}}
                                        {{-- <input type="file" name="product_img[]" class="form-control image-input"
                                                multiple> --}}
                                        {{-- </div> --}}

                                        {{-- Preview Section --}}
                                        {{-- <div id="image-preview" class="d-flex flex-wrap"> --}}
                                        {{-- Preview images will be inserted here --}}
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="border-3 rounded border p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6 d-none">
                                            <label for="inputCostPerPrice" class="form-label">Purchase Cost</label>
                                            <input type="number" step="any" name="current_purchase_cost"
                                                value="0" class="form-control" id="inputCostPerPrice">
                                        </div>
                                        <div class="col-md-6 d-none">
                                            <label for="inputPrice" class="form-label">Sell Price <strong
                                                    class="text-danger">*</strong> </label>
                                            <input type="number" step="any" name="current_sale_price"
                                                id="inputPrice" value="0">
                                        </div>
                                        <div class="col-md-6 d-none">
                                            <label for="inputCompareatprice" class="form-label">Wholesale Price</label>
                                            <input type="number" step="any" name="current_wholesale_price"
                                                id="wholesalepricce" value="0">
                                        </div>

                                        <div class="col-md-6 d-none">
                                            <label for="inputStarPoints" class="form-label">Wholesale
                                                Qty </label>
                                            <input type="number" step="any" name="wholesale_minimum_qty"
                                                class="form-control" id="inputStarPoints" value="0">
                                        </div>

                                        <div class="col-md-6 d-none">
                                            <label for="available_quantity" class="form-label">Available Quantity</label>
                                            <input type="number" step="any" name="available_quantity"
                                                class="form-control" id="available_quantity" value="0">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="inputStarPoints" class="form-label">Discount Type </label>
                                            <select name="discount_type" class="form-control" id=""
                                                onchange="discountType(this)">
                                                <option value="0">Fixed</option>
                                                <option value="1">Percentage (%)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="discount">
                                            <label for="inputStarPoints" class="form-label">Discount Amount</label>
                                            <input type="number" step="any" name="discount" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-md-6" id="stock_alert">
                                            <label for="inputStarPoints" class="form-label">
                                                Stock Alert
                                            </label>
                                            <input type="number" step="any" name="stock_alert" class="form-control"
                                                value="5">
                                        </div>
                                        <div class="col-md-6" id="order_limit">
                                            <label for="inputStarPoints" class="form-label">
                                                Order Limit
                                            </label>
                                            <input type="number" name="order_limit" class="form-control"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" name="is_trending" type="checkbox"
                                                    value="1">
                                                <label class="form-check-label">
                                                    Is Trending
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" name="is_popular" type="checkbox"
                                                    value="1">
                                                <label class="form-check-label" for="flexCheckDisabled">
                                                    Is Popular
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Variants Section -->
                                <div class="border-3 mt-4 rounded border p-4">
                                    <h4 class="card-title">Product Variants</h4>
                                    <div id="variantFields">
                                        <div class="row variant-row g-3 align-items-center" id="variant-1"
                                            data-variant-index="0">
                                            <div class="col-12 variant-row-header">
                                                <span class="variant-index-label">Variant #1</span>
                                            </div>

                                            <div class="col-sm-4">
                                                <input type="number" name="variant_serial_no[]" class="form-control"
                                                    placeholder="Serial No">
                                            </div>
                                            <div class="col-sm-4">
                                                <select name="pack_size[]" class="form-select packSizeSelect"
                                                    id="pack_size_1">
                                                    <option value="">Select Pack Size</option>
                                                    @foreach ($variants as $variant)
                                                        <option value="{{ $variant->id }}"
                                                            data-pack-size="{{ $variant->pack_size }}">
                                                            {{ $variant->pack_size }} - ( {{ $variant->name }} )
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="number" step="any" name="price_per_peice[]"
                                                    class="form-control" id="price_per_peice_1"
                                                    placeholder="Price Per Peice">
                                            </div>
                                            <div class="col-sm-4">
                                                <input type="number" step="any" name="price[]" class="form-control"
                                                    id="price_1" placeholder="Price" readonly>
                                            </div>
                                            <div class="col-sm-4">
                                                <select name="v_brand_ids[]" class="form-select variant_brand_id"
                                                    id="v_brand_id_1">
                                                    <option value="">Select Brand Name</option>
                                                    @foreach ($brand as $brandone)
                                                        <option value="{{ $brandone->id }}">{{ $brandone->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-4">
                                                <select name="stock_status[]" class="form-select" id="stock_status_1">
                                                    <option value="1">In Stock</option>
                                                    <option value="0">Out of Stock</option>
                                                </select>
                                            </div>

                                            <div
                                                class="border-top border-bottom d-flex justify-content-between align-items-center m-2 py-2">
                                                <h6>Variant Sizes</h6>
                                                <button type="button"
                                                    class="btn btn-primary variantSizesRowAddBtn">+</button>
                                            </div>
                                            <div class="col-12 variantSizesContainer mb-3">
                                                {{-- Dynamically using JS --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-primary" id="addVariantBtn">
                                            Add More Variant
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addNewPackSizeModal">
                                            <i class="lni lni-circle-plus"></i>
                                            Add Variant
                                        </button>
                                    </div>
                                </div>

                                <!-- Existing form code below -->
                                <!-- Variants Section -->
                                {{-- <div class="border-3 mt-4 rounded border p-4">
                                    <h4 class="card-title">Product Color Option</h4>
                                    <div id="sizeColorFields">
                                        <div class="row size-color-row mb-2" id="sizeColor-1">
                                            <div class="col-sm-4">
                                                <label for="size_1" class="form-label">Size</label>
                                                <select name="size[]" class="form-select" id="size_1">
                                                    <option value="">Select Size</option>
                                                    @foreach ($sizes as $size)
                                                        <option value="{{ $size->id }}">{{ $size->size }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="color_1" class="form-label">Color</label>
                                                <select name="option[]" class="form-select" id="color_1">
                                                    <option value="">Select Color</option>
                                                    @foreach ($options as $option)
                                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="price_size_1" class="form-label">Price</label>
                                                <input type="number" step="any" name="price_size[]"
                                                    class="form-control" id="price_size_1" placeholder="Price">
                                            </div>
                                        </div>
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
                                        <div class="row productLidOptionRow mb-2" id="productLidOptionRow-1">
                                            <div class="col-sm-3">
                                                <label for="productLidOption-1" class="form-label">
                                                    Product Lid Option
                                                </label>
                                                <select name="productLidOption[]" class="form-select"
                                                    id="productLidOption-1">
                                                    <option value="">Select Lid Option for Product</option>
                                                    @foreach ($lidOptions as $lidOption)
                                                        <option value="{{ $lidOption->id }}">{{ $lidOption->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="productLidOptionPrice-1" class="form-label">
                                                    Price
                                                </label>
                                                <input type="number" step="any" name="productLidOptionPrice[]"
                                                    class="form-control" id="productLidOptionPrice-1"
                                                    placeholder="Product Lid Option Price">
                                            </div>
                                        </div>
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
                                        <div class="row productPackagingOptionRow mb-4" id="productPackagingOptionRow-0">
                                            <div class="col-sm-3">
                                                <label for="productPackagingPrintLocation-0" class="form-label">
                                                    Print Location
                                                </label>
                                                <select name="productPackagingOptions[0][print_location]"
                                                    class="form-select" id="productPackagingPrintLocation-0">
                                                    <option value="">Select Print Location</option>
                                                    <option value="lid">Lid</option>
                                                    <option value="side">Side</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="productPackagingSideOption-0" class="form-label">
                                                    Side Option
                                                </label>
                                                <select name="productPackagingOptions[0][side_option]" class="form-select"
                                                    id="productPackagingSideOption-0">
                                                    <option value="">Select Side</option>
                                                    <option value="front">Front</option>
                                                    <option value="back">Back</option>
                                                    <option value="left">Left</option>
                                                    <option value="right">Right</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="productPackagingPrice-0" class="form-label">
                                                    Price
                                                </label>
                                                <input type="number" step="any"
                                                    name="productPackagingOptions[0][price]" class="form-control"
                                                    id="productPackagingPrice-0"
                                                    placeholder="Product Packaging Option Price">
                                            </div>
                                            <div class="col-sm-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger productPackagingRemoveRowBtn"
                                                    disabled>Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2 text-end">
                                        <button type="button" class="btn btn-primary"
                                            id="addProductPackagingOptionNewRow">Add More</button>
                                    </div>
                                </div>
                                <!-- Product Packaging Options Section Ends -->


                                <div class="row my-3">
                                    <div class="col-md-12">

                                        <div class="mb-3">
                                            <label for="inputProductDescription" class="form-label">Description</label>
                                            <textarea class="form-control" name="description" id="inputProductDescription" rows="3"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="inputAdditionalInformation" class="form-label">Additional
                                                Information</label>
                                            <textarea class="form-control" name="additional_information" id="inputAdditionalInformation" rows="3"></textarea>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @include('adminPanel.partials.seo_form_fields._create')

                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Save Product</button>
                                    </div>
                                </div>
                            </div><!--end row-->
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
                        <label for="inputname" class="col-sm-12 col-form-label pr-0">
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

@section('css_plugins')
    {{-- select2 --}}
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- select2 --}}
    {{-- crop --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
    {{-- crop --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('js_plugins')
    {{-- select 2 --}}
    <script
        src="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js">
    </script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/input-tags/js/tagsinput.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/select2/js/select2-custom.js"></script>
    {{-- select 2 --}}
    {{-- crop --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    {{-- crop --}}
@endsection
@section('js')
    <script>
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

                    // Replace the old index with the new continuous index
                    name = name.replace(/\[\d+\]$/, `[${newIndex}]`);
                    $(this).attr('name', name);
                });
            });
            updateVariantLabels();
        });



        let variantCounter = 1; // Counter to keep track of variant fields

        document.getElementById('addVariantBtn').addEventListener('click', function() {
            variantCounter++;
            const variantFields = document.getElementById('variantFields');

            // Create a new variant row
            const newVariantRow = document.createElement('div');
            newVariantRow.className = 'row variant-row g-3 align-items-center';
            newVariantRow.setAttribute('data-variant-index', variantCounter - 1);
            newVariantRow.id = 'variant-' + variantCounter;
            newVariantRow.innerHTML = `
                                                                                        <div class="col-12 variant-row-header">
                                                                                            <span class="variant-index-label">Variant #${variantCounter}</span>
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <input type="number" name="variant_serial_no[]" class="form-control" placeholder="Serial No">
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <select name="pack_size[]" class="form-select packSizeSelect" id="pack_size_${variantCounter}">
                                                                                                <option value="">Select Pack Size</option>
                                                                                                @foreach ($variants as $variant)
                                                                                                    <option value="{{ $variant->id }}" data-pack-size="{{ $variant->pack_size }}">
                                                                                                        {{ $variant->pack_size }} - ( {{ $variant->name }} )
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <input type="number" step="any" name="price_per_peice[]" class="form-control" id="price_per_peice_${variantCounter}" placeholder="Price Per Piece">
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <input type="number" step="any" name="price[]" class="form-control" id="price_${variantCounter}" placeholder="Price" readonly>
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <select name="v_brand_ids[]" class="form-select variant_brand_id" id="v_brand_id_${variantCounter}">
                                                                                                <option value="">Select Brand Name</option>
                                                                                                @foreach ($brand as $brandone)
                                                                                                    <option value="{{ $brandone->id }}">{{ $brandone->name }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-sm-4">
                                                                                            <select name="stock_status[]" class="form-select" id="stock_status_${variantCounter}">
                                                                                                <option value="1">In Stock</option>
                                                                                                <option value="0">Out of Stock</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div
                                                                                            class="border-top border-bottom d-flex justify-content-between align-items-center m-2 py-2">
                                                                                            <h6>Variant Sizes</h6>
                                                                                            <button type="button"
                                                                                                class="btn btn-primary variantSizesRowAddBtn">+</button>
                                                                                        </div>
                                                                                        <div class="col-12 variantSizesContainer mb-3">
                                                                                            {{-- Dynamically using JS --}}
                                                                                        </div>
                                                                                        <div class="col-sm-4 d-flex align-items-end">
                                                                                            <button type="button" class="btn btn-danger remove-variant" onclick="removeVariant(${variantCounter})">
                                                                                                <i class="lni lni-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    `;
            variantFields.appendChild(newVariantRow);

            if (latestPackSizeOption) {
                const newSelect = newVariantRow.querySelector(`#pack_size_${variantCounter}`);
                newSelect.insertBefore(latestPackSizeOption.clone()[0], newSelect.options[1]);
            }

            updateVariantLabels();
        });

        // Function to remove a variant row
        function removeVariant(variantId) {
            const variantRow = document.getElementById('variant-' + variantId);
            if (variantRow) {
                variantRow.remove();
                updateVariantLabels();
            }
        }

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


        // const imageInput = document.querySelector('.image-input');
        // const previewContainer = document.getElementById('image-preview');
        // let selectedFiles = []; // Array to keep track of selected files

        // imageInput.addEventListener('change', function(event) {
        //     const files = Array.from(event.target.files);
        //     selectedFiles = selectedFiles.concat(files); // Append new files to the array
        //     updatePreview(); // Call to update the preview
        // });

        // function updatePreview() {
        //     previewContainer.innerHTML = ''; // Clear existing previews

        //     selectedFiles.forEach((file, index) => {
        //         const reader = new FileReader();

        //         reader.onload = function(e) {
        //             // Create a wrapper for the image and remove button
        //             const imageWrapper = document.createElement('div');
        //             imageWrapper.classList.add('image-wrapper', 'position-relative', 'm-2');

        //             // Create the image element
        //             const img = document.createElement('img');
        //             img.src = e.target.result;
        //             img.classList.add('img-thumbnail');
        //             img.style.width = '150px'; // Set image preview size
        //             img.style.height = '200px';

        //             // Create the remove button
        //             const removeBtn = document.createElement('button');
        //             removeBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-image');
        //             removeBtn.innerText = ' x ';

        //             // Remove image from preview and deselect from input
        //             removeBtn.addEventListener('click', function() {
        //                 selectedFiles.splice(index, 1); // Remove the file from the array
        //                 updatePreview(); // Update the preview
        //                 imageInput.files = createFileList(selectedFiles); // Update the input file list
        //             });

        //             // Append image and button to the wrapper
        //             imageWrapper.appendChild(img);
        //             imageWrapper.appendChild(removeBtn);

        //             // Append the wrapper to the preview container
        //             previewContainer.appendChild(imageWrapper);
        //         };

        //         reader.readAsDataURL(file);
        //     });
        // }

        // // Helper function to create a new DataTransfer object and update the file input
        // function createFileList(files) {
        //     const dataTransfer = new DataTransfer(); // Use DataTransfer to create a new file list
        //     files.forEach(file => {
        //         dataTransfer.items.add(file);
        //     });
        //     return dataTransfer.files; // Return the new file list
        // }
    </script>


    <script>
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        function getSubcategory(data) {
            var category_id = $(data).val();
            // alert(category_id)

            var url_link = "{{ route('subcategory.list.get') }}"
            $.ajax({
                url: url_link,
                type: "get",
                data: {
                    category_id: category_id,
                },
                success: function(response) {
                    $('#subcategory_id').html(response)
                },
                error: function(xhr) {
                    //Do Something to handle error
                }
            });
        }
    </script>

    <script>
        function discountType(data) {

            if ($(data).val() == 0) {
                $('#discount').html(
                    `<label for="inputStarPoints" class="form-label">Discount Amount</label><input type="number" step="any" name="discount" class="form-control" placeholder="Amount">`
                )
            }
            if ($(data).val() == 1) {
                $('#discount').html(
                    `  <label for="inputStarPoints" class="form-label">Discount (%)</label>
                                                                                                                        <input type="number" step="any" name="discount" class="form-control" placeholder="Percentage (%)">`
                )
            }
        }

        function addnewcolor() {
            // alert('sdfs')
            const color =
                `<span><input type="color" name="product_color[]" class="form-control form-control-color" id="exampleColorInput" value="#563d7c" title="Choose your color"></span>`;
            $('#color').append(color)
        }


        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
            $('#inputProductDescription').summernote({
                height: 200
            });
            $('#inputAdditionalInformation').summernote({
                height: 200
            });

        });

        // let sizeColorCounter = 1; // Counter to keep track of size/color fields

        // // Function to add a new size/color row
        // document.getElementById('addSizeColorBtn').addEventListener('click', function() {
        //     sizeColorCounter++;
        //     const sizeColorFields = document.getElementById('sizeColorFields');

        //     // Create a new size/color row
        //     const newSizeColorRow = document.createElement('div');
        //     newSizeColorRow.className = 'row mb-2 size-color-row';
        //     newSizeColorRow.id = 'sizeColor-' + sizeColorCounter;
        //     newSizeColorRow.innerHTML = `
    //                                                                                 <div class="col-sm-4">
    //                                                                                     <select name="size[]" class="form-select" id="size_${sizeColorCounter}">
    //                                                                                         <option value="">Select Size</option>
    //                                                                                         @foreach ($sizes as $size)
    //                                                                                             <option value="{{ $size->id }}">{{ $size->size }}</option>
    //                                                                                         @endforeach
    //                                                                                     </select>
    //                                                                                 </div>
    //                                                                                 <div class="col-sm-4">
    //                                                                                     <select name="option[]" class="form-select" id="color_${sizeColorCounter}">
    //                                                                                         <option value="">Select Color</option>
    //                                                                                         @foreach ($options as $option)
    //                                                                                             <option value="{{ $option->id }}">{{ $option->name }}</option>
    //                                                                                         @endforeach
    //                                                                                     </select>
    //                                                                                 </div>
    //                                                                                 <div class="col-sm-4 d-flex align-items-end">
    //                                                                                     <div class="input-group">
    //                                                                                         <input type="number" step="any" name="price_size[]" class="form-control" id="price_size_${sizeColorCounter}" placeholder="Price">
    //                                                                                         <button type="button" class="btn btn-danger remove-size-color" onclick="removeSizeColor('sizeColor-${sizeColorCounter}')">
    //                                                                                         <i class="lni lni-trash"></i>

    //                                                                                         </button>
    //                                                                                     </div>
    //                                                                                 </div>
    //                                                                             `;

        //     sizeColorFields.appendChild(newSizeColorRow);
        // });

        // // Function to remove a size/color row
        // function removeSizeColor(sizeColorId) {
        //     const sizeColorRow = document.getElementById(sizeColorId);
        //     if (sizeColorRow) {
        //         sizeColorRow.remove();
        //     }
        // }

        $('#inputProductSlug').on('keyup', function() {

            var product_slug = $(this).val();
            var url_link = "{{ route('product.slug.validate') }}";
            $.ajax({
                url: url_link,
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
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
                    //Error
                }
            });
        });


        $(document).on('click', '.dropdown-toggle', function(ev) {
            $(this).parent().find('.dropdown-menu').show();
        });

        $(document).on('click', '.note-editable.card-block', function() {
            $('.note-dropdown-menu.dropdown-menu').hide();
        });


        function calculatePrice(index) {
            var pricePerPiece = parseFloat($('#price_per_peice_' + index).val());
            var packSize = parseFloat($('#pack_size_' + index).find('option:selected').data('pack-size'));

            if (!isNaN(pricePerPiece) && !isNaN(packSize)) {
                var price = pricePerPiece * packSize;
                $('#price_' + index).val(price.toFixed(2));
            } else {
                $('#price_' + index).val('');
            }
        }

        /* price per piece input */
        $(document).on('input', '[id^="price_per_peice_"]', function() {
            var index = this.id.split('_')[3];
            calculatePrice(index);
        });

        /* pack size select */
        $(document).on('change', '[id^="pack_size_"]', function() {
            var index = this.id.split('_')[2];
            calculatePrice(index);
        });



        var newLidOptionRowID = 1;
        $(document).on('click', '#addProductLidOptionNewRow', function() {

            newLidOptionRowID++;
            var newLidOptionRow = `
                                                                                                <div class="row mb-2 productLidOptionRow" id="productLidOptionRow-${newLidOptionRowID}">
                                                                                                    <div class="col-sm-3">
                                                                                                        <label for="productLidOption-${newLidOptionRowID}" class="form-label">
                                                                                                            Product Lid Option
                                                                                                            <strong class="text-danger">*</strong>
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
                                                                                                            <strong class="text-danger">*</strong>
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



        document.getElementById('add-more-button-x').addEventListener('click', function() {
            var table = document.getElementById('product-images-table-x');
            var newRow = table.querySelector('.product-image-row').cloneNode(true);
            newRow.querySelector('.preview-image-x').src = '';
            newRow.querySelector('.image-input-x').value = '';
            newRow.querySelector('input[name="product_img_alt[]"]').value = '';
            newRow.querySelector('input[name="product_img_name[]"]').value = '';

            newRow.querySelector('.remove-row-button-x').classList.remove('d-none');
            newRow.querySelector('.remove-row-button-x').classList.add('d-block');


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
                row.parentNode.removeChild(row);
            }
        });




        let latestPackSizeOption = null;

        function saveNewPackSize() {
            let type = $('#inputtype').val();
            let name = $('#nameInput').val();
            let packSize = $('#pakSizeInput').val();

            $.ajax({
                url: "{{ route('admin.product.saveNewPackSize') }}",
                type: 'POST',
                data: {
                    type: type,
                    name: packSize,
                    pack_size: packSize,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Create new option element
                    latestPackSizeOption = $('<option>', {
                        value: response.id,
                        text: response.pack_size + ' - ' + response.name,
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

        let packagingRowCount = 1;


        $(document).on('change', 'select[id^="productPackagingPrintLocation"]', function() {
            var rowId = $(this).attr('id').split('-')[1];
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

        // Product Packaging Options Section Ends


        //$(document).ready(function () {
        //    // Event listener for the first select
        //    $('#brandSelect').on('change', function () {
        //    const selectedBrands = $(this).val(); // Get selected brand IDs
        //    // Update the options in all variant brand selects
        //    $('.variant_brand_id').each(function () {
        //            const $select = $(this);
        //            $select.find('option').each(function () {
        //                const optionValue = $(this).val();
        //                $(this).prop('disabled', !selectedBrands.includes(optionValue)); // Enable or disable based on selection
        //            });
        //            // Reset to the first option or first available brand if needed
        //            if (selectedBrands.length === 0) {
        //                $select.val('');
        //            }
        //        });
        //    });
        //});
    </script>
@endsection
