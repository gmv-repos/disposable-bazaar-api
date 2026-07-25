@extends('adminPanel.layout.layout')

@section('main_content')
    <div class="page-content">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Quotation</h4>
            </div>
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('quotations.update', $quotation->id) }}" method="POST" id="quotationForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="quotation_date" class="form-label">Quotation Date</label>
                                <input type="date" name="quotation_date" id="quotation_date" class="form-control"
                                    value="{{ date('Y-m-d', strtotime($quotation->quotation_date)) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control"
                                    value="{{ $quotation->customer_name }}" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control"
                                    value="{{ $quotation->company_name }}">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="valid_until" class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" id="valid_until" class="form-control"
                                    value="{{ date('Y-m-d', strtotime($quotation->valid_until)) }}" required>
                            </div>

                            <div class="col-md-12 mb-3 mt-5 bg-secondary py-2 rounded">
                                <input type="text" class="form-control" name="search" id="search"
                                    placeholder="Search Product or Brand Name" onkeydown="searchProductByNameOrBrand(this)">
                                <div id="productsResults"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table w-100" id="QuotationItemsTable">
                                        <thead>
                                            <tr class="bg-light text-dark rounded">
                                                <th>Img</th>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Variants</th>
                                                <th>Price Per Piece</th>
                                                <th>QTY</th>
                                                <th>Total</th>
                                                <th>-</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($quotation->quotationItems as $qItem)
                                                <tr class="bg-light text-dark rounded" productID="{{ $qItem->product_id }}">
                                                    <input type="hidden" name="hiddenProductIDs[]"
                                                        value="{{ $qItem->product_id }}">
                                                    <input type="hidden" name="hiddenProductPrices[]"
                                                        value="{{ $qItem->price }}">
                                                    <input type="hidden" name="hiddenProductQTYs[]"
                                                        value="{{ $qItem->quantity }}">
                                                    <input type="hidden" name="hiddenProductDiscounts[]"
                                                        value="{{ $qItem->discount }}">
                                                    <input type="hidden" name="product_variant_ids[]"
                                                        value="{{ $qItem->product_variant_id }}">
                                                    <input type="hidden" name="brand_ids[]"
                                                        value="{{ $qItem->brand_id }}">
                                                    <td>
                                                        <img src="{{ asset($qItem->product->image) }}" alt="Product Image"
                                                            class="img-fluid" style="width: 50px; height: 50px;">
                                                    </td>
                                                    <td>{{ $qItem->product->name }}</td>
                                                    <td>
                                                        <select class="form-select form-select-sm w-100"
                                                            name="brandSelect[]" onchange="updateBrandId(this)">
                                                            @foreach ($brands as $brand)
                                                                <option value="{{ $brand->id }}"
                                                                    {{ $qItem->brand_id == $brand->id ? 'selected' : '' }}>
                                                                    {{ $brand->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm w-100 variantSelect"
                                                            name="variantSelect[]" onchange="onVariantChange(this)">
                                                            <option value="" data-price="0">Custom</option>

                                                            @php
                                                                $variants = \App\Models\ProductVariant::with('variant')
                                                                    ->where('product_id', $qItem->product_id)
                                                                    ->get();
                                                            @endphp

                                                            @foreach ($variants as $pv)
                                                                <option value="{{ $pv->id }}"
                                                                    data-price="{{ $pv->price }}"
                                                                    {{ $qItem->product_variant_id == $pv->id ? 'selected' : '' }}>
                                                                    {{ $pv->variant?->pack_size ?? 'N/A' }}
                                                                </option>
                                                            @endforeach

                                                            <option value="0" data-price="0">No Variant</option>
                                                        </select>

                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control w-50 itemPrice"
                                                            name="itemPrice" value="{{ $qItem->price }}" step="any"
                                                            min="1" oninput="calculateTotal()">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control w-50 itemQtyInput"
                                                            value="{{ $qItem->quantity }}" min="1"
                                                            oninput="calculateTotal()">
                                                    </td>

                                                    <td class="itemTotalPrice">{{ $qItem->total }}</td>
                                                    <td>
                                                        <i class="lni lni-trash" style="cursor: pointer;"
                                                            onclick="removeItem(this)"></i>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>Grand Total</div>
                                    <div id="grandTotal">{{ $quotation->total }}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">

                                    <div class="">
                                        <select name="discountType" class="form-select form-select-sm" id="discountType"
                                            onchange="calculateTotal()">
                                            <option value="0">Fixed</option>
                                            <option value="1">Percentage (%)</option>
                                        </select>
                                    </div>

                                    <div class="">
                                        <input type="number" id="discountInput" name="discountInput"
                                            oninput="calculateTotal()" class="form-control" step="0.01"
                                            value="{{ $quotation->discount }}">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <b>TAX</b>
                                    <input type="number" name="taxAmount" id="taxAmount"
                                        class="form-control form-control-sm w-50" value="{{ $quotation->tax }}"
                                        oninput="calculateTotal()" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>Payable Amount</div>
                                    <div id="payableAmount"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <hr />
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="Note"></textarea>
                        </div>

                        <div class="col-md-12 mt-3 text-center">
                            <button type="submit" class="btn btn-primary w-50">
                                Update Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        calculateTotal();

        function searchProductByNameOrBrand(input) {
            let value = $(input).val();
            $.ajax({
                url: "{{ route('admin.search.product.by.name.or.brand') }}",
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
                        onclick="addProductToQuotationList(${product.id})">
                        <span>${product.name}</span>
                        <span class="badge text-bg-primary">${product.brand.name}</span>
                    </li>
                `;
                });
                $('#productsResults').html(`<ul class="list-group">${resultHTML}</ul>`);
            } else {
                $('#productsResults').html('<p class="text-danger mt-2">No products found</p>');
            }
        }

        // function addProductToList(id) {
        //     $.ajax({
        //         url: "{{ route('admin.get.product.by.id') }}",
        //         type: "GET",
        //         data: {
        //             id: id
        //         },
        //         success: function(response) {
        //             let newItem = response.productRow;
        //             $('#QuotationItemsTable tbody').append(newItem);
        //             calculateTotal();
        //             $('#search').val('');
        //             $('#productsResults').html('');
        //         }
        //     });
        // }

        function addProductToQuotationList(id) {

            $.ajax({
                url: "{{ route('quotations.addProductToQuotationList') }}",
                type: "GET",
                data: {
                    id: id
                },
                success: function(response) {
                    let newItem = response.productRow;
                    $('#QuotationItemsTable tbody').append(newItem);
                    calculateTotal();
                    $('#search').val('');
                    $('#productsResults').html('');
                }
            });

        }

        function removeItem(item) {
            $(item).closest('tr').remove();
            calculateTotal();
        }

        function onVariantChange(selectElement) {
            let selectedOption = $(selectElement).find(':selected');
            let price = parseFloat(selectedOption.data('price')) || 0;
            let variantId = selectedOption.val();

            let row = $(selectElement).closest('tr');
            row.find('.itemPrice').val(price);
            row.find('.itemTotalPrice').text(parseFloat(price));
            row.find("input[name='product_variant_ids[]']").val(variantId);
            calculateTotal();
        }

        function updateBrandId(selectElement) {
            let brandId = $(selectElement).val();
            let row = $(selectElement).closest('tr');
            row.find("input[name='brand_ids[]']").val(brandId);
        }

        // Update brand selects to trigger hidden field update on load
        $(document).ready(function() {
            $('select[name="brandSelect[]"]').each(function() {
                updateBrandId(this);
            });
        });

        // Initial calculation
        calculateTotal();

        function calculateTotal() {
            let grandTotal = 0;

            $('#QuotationItemsTable tbody tr').each(function() {
                // Update price, qty, discount
                let itemPrice = parseFloat($(this).find('.itemPrice').val()) || 0;
                let itemQuantity = parseFloat($(this).find('.itemQtyInput').val()) || 0;
                let itemDiscount = parseFloat($(this).find('.itemDiscountInput').val()) || 0;

                // Update hidden fields
                $(this).find("input[name='hiddenProductPrices[]']").val(itemPrice);
                $(this).find("input[name='hiddenProductQTYs[]']").val(itemQuantity);
                $(this).find("input[name='hiddenProductDiscounts[]']").val(itemDiscount);

                // Calculate total
                let itemTotal = (itemPrice * itemQuantity) - itemDiscount;
                $(this).find('.itemTotalPrice').text(itemTotal.toFixed(2));
                grandTotal += itemTotal;
            });

            // Update totals
            $('#grandTotal').text(grandTotal.toFixed(2));

            // Handle discount calculations
            let discountType = +$('#discountType').val();
            let discountInput = +$('#discountInput').val() || 0;
            let discountAmount = discountType === 1 ? (grandTotal * discountInput) / 100 : discountInput;

            // Handle tax calculations
            let taxAmount = +$('#taxAmount').val() || 0;
            let payableAmount = grandTotal - discountAmount + taxAmount;

            $('#payableAmount').text(payableAmount.toFixed(2));
        }
    </script>
@endsection
