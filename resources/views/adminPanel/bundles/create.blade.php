@extends('adminPanel.layout.layout')

@section('main_content')
    <div class="page-content">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Bundle</h4>
            </div>
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('bundles.store') }}" method="POST" id="bundleForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="bundleName">Bundle Name</label>
                                <input type="text" class="form-control" name="bundleName" id="bundleName">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bundleSlug">Slug</label>
                                <input type="text" class="form-control" name="bundleSlug" id="bundleSlug">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bundleImages">Images (Multiple)</label>
                                <input type="file" class="form-control" name="bundleImages[]" id="bundleImages" multiple>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Description"></textarea>
                            </div>

                            <div class="col-md-12 mb-3 mt-5 bg-secondary py-2 rounded">
                                <input type="text" class="form-control" name="search" id="search"
                                    placeholder="Search Product or Brand Name" onkeydown="searchProductForBundle(this)">
                                <div id="productsResults"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table w-100" id="bundleProductsTable">
                                        <thead>
                                            <tr class="bg-light text-dark text-center rounded">
                                                <th scope="col">IMG</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Brand</th>
                                                <th scope="col">Variant</th>
                                                <th scope="col">Lid</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Total Price</th>
                                                <th scope="col">-</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Bundle Products List  -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td>Discount</td>
                                                <td>
                                                    <input type="number" name="bundleDiscount"
                                                        class="form-control form-control-sm" id="bundleDiscount"
                                                        oninput="calculateTotal()">
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
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                @include('adminPanel.partials.seo_form_fields._create')
                            </div>
                        </div>
                        <div class="col-md-12 mt-3 text-center">
                            <button type="submit" class="btn btn-primary w-50">
                                Create Bundle
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
        function searchProductForBundle(input) {
            let value = $(input).val();
            $.ajax({
                url: "{{ route('bundles.searchProductForBundle') }}",
                type: "GET",
                data: {
                    searchTerm: value
                },
                success: function (response) {
                    displaySearchResults(response.searchResult);
                }
            });
        }

        function displaySearchResults(searchResult) {
            if (searchResult && searchResult.length > 0) {
                let resultHTML = '';
                searchResult.forEach(function (product) {
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

        function productVariantChange(data) {
            let vPackSizePrice = +$(data).find('option:selected').attr('vPackSizePrice');
            $(data).closest('tr').find('.productPriceInput').val(vPackSizePrice);
            calculateTotal();
        }


        function calculateTotal() {
            let bundleTotal = 0;

            $('#bundleProductsTable tbody tr').each(function () {
                let productPrice = parseFloat($(this).find('.productPriceInput').val() || 0);
                let productQuantity = parseFloat($(this).find('.productQtyInput').val());
                $(this).find("input[name='hiddenProductQTYs[]']").val(productQuantity);


                let productTotal = (productPrice * productQuantity);

                $(this).find('.productTotalPrice').text(parseFloat(productTotal));

                bundleTotal += productTotal;

            });

            let bundleDiscount = $('#bundleDiscount').val() || 0;
            let grandTotal = bundleTotal - bundleDiscount;
            $('#grandTotal').text(grandTotal.toFixed(2));
        }
    </script>

@endsection