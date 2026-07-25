@extends('adminPanel.layout.layout')

@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <div>
            <div class="border border-3 p-4 rounded">
                <div class="row">
                    <div class="col-sm-10">
                        <h2>Product Details</h2>
                    </div>

                    <div class="col-sm-2 text-right">
                        <a href="{{ route('admin.product.list') }}">
                            <div class="menu-title">
                                <span class="add-menu-sidebar"
                                    style="display: flex; justify-content: center; align-items: center">
                                    <span class="nav-text text-center text-white"><i class="lni lni-circle-plus"></i>Product
                                        List</span>
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="border border-3 p-4 mt-4 rounded">


                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <h5>Product Information</h5>
                            <ul class="product-info">
                                <li><strong>Name:</strong> {{ $product->name }}</li>
                                <li><strong>Code:</strong> {{ $product->code }}</li>
                                <li><strong>Category:</strong> {{ $product->productCategory->name }}</li>
                                <li><strong>Color:</strong> {{ $product->color }}</li>
                                <li><strong>Brand:</strong> {{ $product->brand->name ?? 'N/A' }}</li>
                                <li><strong>Discount:</strong> {{ $product->discount }}% off</li>
                                <li><strong>Supplier:</strong> {{ $product->supplier->supplier_name ?? 'N/A' }}</li>
                                <li><strong>Stock Alert:</strong> {{ $product->stock_alert }}</li>
                                <li><strong>Status:</strong> {{ $product->status == 1 ? 'Active' : 'Inactive' }}</li>
                            </ul>
                        </div>

                        <div class="col-sm-6 text-right">
                            <h5>Images</h5>
                            <div class="product-images">
                                @foreach($product->productImage as $image)
                                    <img class="item_img_st" src="{{ asset($image->image) }}" alt="Product Image">
                                @endforeach
                            </div>
                        </div>
                        {{-- <div class="col-sm-6 text-right">
                            <h5>Pricing</h5>
                            <ul class="pricing-info">
                                <li><strong>Current Purchase Cost:</strong> {{ round($product->current_purchase_cost, 2) }}
                                </li>
                                <li><strong>Current Sale Price:</strong> {{ round($product->current_sale_price, 2) }}</li>
                                <li><strong>Wholesale Price:</strong> {{ round($product->current_wholesale_price, 2) }}</li>
                                <li><strong>Discount:</strong> {{ $product->discount }}% off</li>
                            </ul>
                        </div> --}}
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <h5>Description</h5>
                            <p>{!! $product->description !!}</p>
                        </div>
                    </div>
                </div>
                <div class="border border-3 p-4 mt-4 rounded">
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <h5>Product Variants</h5>
                            <table class="table table-responsive">
                                <thead class="bgclset">
                                    <tr>
                                        <th>S.NO.</th>
                                        <th>PACK SIZE</th>
                                        <th>PRICE</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->productVariants as $key => $variant)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $variant->variant->pack_size }}</td>
                                            <td>{{ round($variant->price, 2) }}</td>
                                            <td>{{ $variant->status == 1 ? 'Available' : 'Unavailable' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="border border-3 p-4 mt-4 rounded">

                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <h5>Product Options</h5>
                            <table class="table table-responsive">
                                <thead class="bgclset">
                                    <tr>
                                        <th>S.NO.</th>
                                        <th>SIZE</th>
                                        <th>OPTION</th>
                                        <th>OPTIONS PRICE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->productOptions as $key => $option)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $option->size->size }}</td>
                                            <td>{{ $option->option->name }}</td>
                                            <td>{{ round($option->options_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection