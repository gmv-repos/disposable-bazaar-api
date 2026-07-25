@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Bundle</h4>
        </div>
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{ route('bundles.update', $bundle->id) }}" method="POST" id="bundleForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="bundleName">Bundle Name</label>
                            <input type="text" class="form-control" name="bundleName" value="{{ $bundle->name }}" id="bundleName">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bundleSlug">Slug</label>
                            <input type="text" class="form-control" name="bundleSlug" value="{{ $bundle->slug }}" id="bundleSlug">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bundleImages">Images (Multiple)</label>
                            <input type="file" class="form-control" name="bundleImages[]" id="bundleImages" multiple>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="3"
                                placeholder="Description">{{ $bundle->description }}</textarea>
                        </div>

                        <div class="col-md-12 mb-3 mt-5 bg-secondary py-2 rounded">
                            <input type="text" class="form-control" name="search" id="search" placeholder="Search Product or Brand Name" onkeydown="searchProductForBundle(this)">
                            <div id="productsResults"></div>
                        </div>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table w-100" id="bundleProductsTable">
                                    <thead>
                                        <tr class="bg-light text-dark text-center rounded">
                                            <th>IMG</th>
                                            <th>Product Name</th>
                                            <th>Brand</th>
                                            <th>Variant</th>
                                            <th>Lid</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total Price</th>
                                            <th>-</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bundle->bundleItems as $item)
                                            <tr class="bg-light text-dark rounded align-middle" productID="{{ $item->product->id }}">
                                                <input type="hidden" name="hiddenProductIDs[]" value="{{ $item->product->id }}">

                                                <td>
                                                    <img class="pimgst" src="{{ asset($item->product->image_path ?: 'assets/adminPanel/images/dummy.png') }}" alt="{{ $item->product->name }}">
                                                </td>

                                                <td>{{ $item->product->name }}</td>

                                                <td>
                                                    <select class="form-control productBrandSelect" name="productBrands[]">
                                                        <option value="">Select Brand</option>
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ $brand->id == $item->brand_id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    @if ($item->product->productVariants->isNotEmpty())
                                                        <select class="form-control productVariantSelect" name="productVariants[]" onclick="productVariantChange(this)">
                                                            <option value="custom" {{ $item->product_variant_id ? '' : 'selected' }}>custom</option>
                                                            @foreach ($item->product->productVariants as $productVariant)
                                                                @php
                                                                    $packSizePrice = $productVariant->price_per_peice * $productVariant->variant->pack_size;
                                                                @endphp
                                                                <option value="{{ $productVariant->id }}" vPackSizePrice="{{ $packSizePrice }}" {{ $productVariant->id == $item->product_variant_id ? 'selected' : '' }}>
                                                                    {{ $productVariant->variant->pack_size }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        N/A <input type="hidden" class="productVariantSelect" name="productVariants[]" value="">
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($item->product->productLidOptions->isNotEmpty())
                                                        <select class="form-control productLidSelect" name="productLids[]" onchange="productLidChange(this)">
                                                            <option value="">Select Lid</option>
                                                            @foreach ($item->product->productLidOptions as $lidOption)
                                                                <option value="{{ $lidOption->id }}" lidPrice="{{ $lidOption->price }}" {{ $lidOption->id == $item->product_lid_option_id ? 'selected' : '' }}>
                                                                    {{ $lidOption->lidOption->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        N/A <input type="hidden" class="productLidSelect" name="productLids[]" value="">
                                                    @endif
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control productPriceInput" name="productPrices[]" value="{{ $item->price }}" min="1" oninput="calculateTotal()">
                                                </td>

                                                <td>
                                                    <input type="number" class="form-control productQtyInput" name="productQTYs[]" value="{{ $item->quantity }}" min="1" oninput="calculateTotal()">
                                                </td>

                                                <td class="productTotalPrice">{{ $item->price * $item->quantity }}</td>

                                                <td><i class="lni lni-trash" style="cursor: pointer;" onclick="removeItem(this)"></i></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr><td colspan="8"></td></tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Discount</td>
                                            <td>
                                                <input type="number" name="bundleDiscount" class="form-control form-control-sm" id="bundleDiscount" oninput="calculateTotal()" value="{{ $bundle->discount_amount }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Grand Total</td>
                                            <td id="grandTotal"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @php
                            $seoMetadata = json_decode(json_encode([
                                'meta_title' => $bundle->meta_title,
                                'canonical_url' => $bundle->canonical_url,
                                'focus_keyword' => $bundle->focus_keyword,
                                'redirect_301' => $bundle->redirect_301,
                                'redirect_302' => $bundle->redirect_302,
                                'schema' => $bundle->schema,
                            ]));
                        @endphp
                        @include('adminPanel.partials.seo_form_fields._edit', [
                            'seoMetadata' => $seoMetadata,
                        ])
                        <div class="col-md-12 mt-3 text-center">
                            <button type="submit" class="btn btn-primary w-50">Update Bundle</button>
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
    function searchProductForBundle(input) {
        let value = $(input).val();
        $.ajax({
            url: "{{ route('bundles.searchProductForBundle') }}",
            type: "GET",
            data: {
                searchTerm: value
            },
            success: function(response) {
                displaySearchResults(response.searchResult);
            }
        });
    }

    function displaySearchResults(searchResult) {
        if (searchResult && searchResult.length > 0) {
            let resultHTML = '';
            searchResult.forEach(function(product) {
                resultHTML += `
                    <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                        style="cursor: pointer;" 
                        onclick="addProductToBundle(${product.id})">
                        <span>${product.name}</span>
                    </li>
                `;
            });
            $('#productsResults').html(`<ul class="list-group">${resultHTML}</ul>`);
        } else {
            $('#productsResults').html('<p class="text-danger mt-2">No products found</p>');
        }
    }

        function addProductToBundle(id) {
            const $table = $('#bundleProductsTable tbody');
            const $existingRow = $table.find(`tr[productID="${id}"]`);

            let currentProductCount = $table.find('tr').length;
            if (currentProductCount >= 10) {
                Toastify({
                    text: "You can't add more than 10 products to the bundle.",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #e74c3c, #e67e22)",
                }).showToast();
                $('#productsResults').html('');
                return;
            }

            if ($existingRow.length > 0) {
                const $qtyInput = $existingRow.find('.productQtyInput');
                const currentQty = parseInt($qtyInput.val()) || 1;
                $qtyInput.val(currentQty + 1);
                calculateTotal();
                $('#search').val('');
                $('#productsResults').html('');
                return;
            }

            $.ajax({
                url: "{{ route('bundles.addProductToBundle') }}",
                type: "GET",
                data: { id },
                success: function (response) {
                    $table.append(response.productRow);
                    calculateTotal();
                    $('#search').val('');
                    $('#productsResults').html('');
                },
                error: function () {
                    Toastify({
                        text: "Something went wrong. Please try again.",
                        duration: 3000,
                        backgroundColor: "#e74c3c",
                    }).showToast();
                }
            });
        }

    function removeItem(item) {
        $(item).closest('tr').remove();
        calculateTotal();
    }

    function productVariantChange(select) {
        let vPackSizePrice = +$(select).find('option:selected').attr('vPackSizePrice');
        $(select).closest('tr').find('.productPriceInput').val(vPackSizePrice);
        calculateTotal();
    }

    function calculateTotal() {
        let bundleTotal = 0;
        $('#bundleProductsTable tbody tr').each(function() {
            let price = parseFloat($(this).find('.productPriceInput').val() || 0);
            let qty = parseFloat($(this).find('.productQtyInput').val() || 0);
            let total = price * qty;
            $(this).find('.productTotalPrice').text(total.toFixed(2));
            bundleTotal += total;
        });
        let discount = parseFloat($('#bundleDiscount').val() || 0);
        let grandTotal = bundleTotal - discount;
        $('#grandTotal').text(grandTotal.toFixed(2));
    }

    $(document).ready(function() {
        calculateTotal();
    });
</script>
@endsection
