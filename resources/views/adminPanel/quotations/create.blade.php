@extends('adminPanel.layout.layout')

@section('main_content')
    <div class="page-content">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create Quotation</h4>
            </div>
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('quotations.store') }}" method="POST" id="quotationForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="quotation_date" class="form-label">Quotation Date</label>
                                <input type="date" name="quotation_date" id="quotation_date" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="valid_until" class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" id="valid_until" class="form-control" required>
                            </div>

                            <div class="col-md-12 mb-3 mt-2 bg-secondary py-2 rounded">
                                <input type="text" class="form-control" name="search" id="search"
                                    placeholder="Search Product or Brand Name" onkeydown="searchProductByNameOrBrand(this)">
                                <div id="productsResults"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table w-100" id="QuotationItemsTable">
                                        <thead>
                                            <tr class="bg-light text-dark rounded">

                                                {{-- <th>IMG</th> --}}
                                                <th>IMG</th>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Variants</th>
                                                <th>Price Per Piece</th>
                                                <th>QTY</th>
                                                <th>Total</th>
                                                <th>
                                                    -
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Quotation Items List  -->
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <b>Grand Total</b>
                                    <div id="grandTotal"></div>
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
                                            oninput="calculateTotal()" class="form-control">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <b>TAX</b>
                                    <input type="number" name="taxAmount" id="taxAmount"
                                        class="form-control form-control-sm w-50" oninput="calculateTotal()" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <b>Payable Amount</b>
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
                                Create Quotations
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


        function calculateTotal() {
            let grandTotal = 0;

            $('#QuotationItemsTable tbody tr').each(function() {

                let itemPrice = parseFloat($(this).find('.itemPrice').val()) || 0;
                let itemQuantity = parseFloat($(this).find('.itemQtyInput').val()) || 0;
                let itemDiscount = parseFloat($(this).find('.itemDiscountInput').val()) || 0;
                let itemTotal = (itemPrice * itemQuantity) - itemDiscount;

                $(this).find("input[name='hiddenProductPrices[]']").val(itemPrice);
                $(this).find("input[name='hiddenProductQTYs[]']").val(itemQuantity);
                $(this).find("input[name='hiddenProductDiscounts[]']").val(itemDiscount);

                $(this).find('.itemTotalPrice').text(parseFloat(itemTotal));

                grandTotal += itemTotal;

            });


            $('#grandTotal').text(grandTotal.toFixed(2));

            //Discount Caluction
            let discountType = +$('#discountType').val();
            let discountInput = +$('#discountInput').val() || 0;

            let discountAmount = discountInput;
            if (discountType == 1) {
                discountAmount = (grandTotal * discountInput) / 100;
            }

            let taxAmount = +$('#taxAmount').val() || 0;
            let payableAmount = grandTotal - discountAmount + taxAmount;
            $('#payableAmount').text(payableAmount.toFixed(2));

        }

        function onVariantChange(selectElement) {
            console.log("awdawdawdawd");
            let selectedOption = $(selectElement).find(':selected');
            let price = parseFloat(selectedOption.data('price')) || 0;

            let row = $(selectElement).closest('tr');
            row.find('.itemPrice').val(price);

            calculateTotal();
        }
    </script>
@endsection
