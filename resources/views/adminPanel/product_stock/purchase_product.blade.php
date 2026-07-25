@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <input type="hidden" id="submittype" value="1">
        <input type="hidden" id="pageNo" value="0">
        <div class="row">
            <div class="col-sm-12 mt-2" style="padding: 0px;">
                <form action="{{ route('purchase.payment.store') }}" method="post" id="paymentshow">
                    @csrf
                    <div class="card card-body">
                        <input type="hidden" name="hiddenTotalPayable" id="hiddenTotalPayable">
                        <input type="hidden" name="hiddenTotalCost" id="hiddenTotalCost">
                        <div class="row d-flex justify-content-center align-items-center posTopbar">
                            <div class="col-sm-8 d-flex align-items-center">
                                <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                    <option value="">Choose Supplier</option>
                                    @foreach ($supplierList as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}
                                            ({{ $supplier->supplier_phone_one }})
                                        </option>
                                    @endforeach

                                </select>

                                <div class="bg-light d-flex ms-3 rounded px-3 py-1">
                                    <div class="form-check form-check-inline bg-light mx-4">
                                        <input class="form-check-input" type="radio" name="purchaseType" id="poOption"
                                            value="PO" required>
                                        <label class="form-check-label fw-bold" for="poOption">PO</label>
                                    </div>

                                    <div class="form-check form-check-inline mx-4">
                                        <input class="form-check-input" type="radio" name="purchaseType" id="grnOption"
                                            value="GRN">
                                        <label class="form-check-label fw-bold" for="grnOption">GRN</label>
                                    </div>
                                </div>


                            </div>
                            <div class="col-sm-4 d-flex justify-content-end">
                                {{-- <span class="addCustomer" onclick="customeradd()"><i style="font-size: 18px;"
                                    class="lni lni-circle-plus"></i> &nbsp; Supplier</span> --}}
                            </div>
                        </div>
                        <table class="w-100 table">
                            <thead>
                                <tr class="bg-light text-dark py-3">
                                    <th style="width: 5%">IMG</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    {{-- <th>Varient/Custom</th> --}}
                                    <th>Price Per Piece</th>
                                    <th>QTY</th>
                                    {{-- <th>Total Pieces</th> --}}
                                    <th>Total</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="orderList">

                            </tbody>
                        </table>
                        <div class="mt-4">
                            <div class="posfooter_st">
                                <div class="purchase_footer_first">

                                </div>
                                <div class="purchase_footer_second totaltx" style="padding-left: 35px;">
                                    <span>Subtotal Total</span>
                                </div>
                                <div class="purchase_footer_third">
                                    <strong id="subTotal">00</strong>
                                </div>
                            </div>

                            <div class="posfooter_st">
                                <div class="purchase_footer_first">
                                    <div style="display: flex;padding: 0px 10px" class="discountinput">
                                        <select name="discount_type" class="form-select" id="distypeset"
                                            onchange="countTotal()">
                                            <option value="1">Percentage (%)</option>
                                            <option value="0" class="d-none">Fixed</option>
                                        </select> &nbsp;
                                        <input type="number" id="discountInput" oninput="countTotal()"
                                            class="form-control">
                                    </div>

                                </div>
                                <div class="purchase_footer_second totaltx" style="padding-left: 35px;">
                                    <span>Total Discount</span>
                                </div>
                                <div class="purchase_footer_third" style="text-align: right">
                                    <strong id="discountAmount" class="total_discount_txt">00</strong>

                                    <input type="hidden" id="hiddenDiscountAmount" name="hiddenDiscountAmount"
                                        value="0">
                                </div>
                            </div>

                            <div class="posfooter_st">
                                <div class="purchase_footer_first">
                                    <div class="d-flex align-items-center" style="padding: 0px 10px">
                                        <b class="mx-4">Delivery</b>
                                        <input type="number" name="deliveryCharges" id="deliveryCharges"
                                            oninput="countTotal()" class="form-control form-control-sm" value="0"
                                            placeholder="Delivery Charges">
                                    </div>
                                </div>
                                <div class="purchase_footer_second totaltx" style="padding-left: 35px;">
                                    <span>Payable Amount</span>
                                </div>
                                <div class="purchase_footer_third" style="text-align: right">
                                    <strong id="totaPayable" class="total_discount_txt">00</strong>

                                </div>
                            </div>

                            <div class="row d-flex justify-content-center mt-4">
                                <div class="col-sm-3 d-flex justify-content-center">
                                    <button type="burron" onclick="submittype(2)" class="addCustomer">Submit</button>
                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

            <div class="col-md-12 bg-secondary mb-3 mt-2 rounded py-2">
                <h3 class="ps-3 text-white">Add Product</h3>
                <div class="leftpos">
                    <input type="text" class="form-control" name="search" id="search"
                        placeholder="Search Product or Brand Name" oninput="searchProductByNameOrBrand(this)">
                    <div id="productsResults"></div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="customeradd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="" id="customeaddSubmit">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create Supplier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6" style="border-right:1px solid #dfdada">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <h6 class="titleheadst">Supplier Info</h6>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Supplier Name
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="supplier_name" class="form-control"
                                                    name="supplier_name" placeholder="Supplier Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Supplier Phone
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="supplier_phone_one" class="form-control"
                                                    name="supplier_phone_one" placeholder="Supplier Phone" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Supplier Phone
                                                Two
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="supplier_phone_two" class="form-control"
                                                    name="supplier_phone_two" placeholder="Supplier Phone Two">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Supplier Email
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="supplier_email" class="form-control"
                                                    name="supplier_email" placeholder="Supplier Email">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="supplier_address" class="col-sm-12 col-form-label pr-0">Supplier
                                                Address
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea name="supplier_address" class="form-control" id="supplier_address" cols="10" rows="3"
                                                    placeholder="Supplier Address"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-6" style="border-right:1px solid #dfdada">
                                    <div class="col-sm-12">
                                        <h6 class="titleheadst">Company Info</h6>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label for="company_name" class="col-sm-12 col-form-label pr-0">Company
                                                Name
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="company_name" class="form-control"
                                                    name="company_name" placeholder="Company Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="company_phone" class="col-sm-12 col-form-label pr-0">Company
                                                Phone
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="company_phone" class="form-control"
                                                    name="company_phone" placeholder="Company Phone" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="com_email" class="col-sm-12 col-form-label pr-0">Company Email
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="company_email" class="form-control"
                                                    name="company_email" placeholder="Company Email">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="due" class="col-sm-12 col-form-label pr-0">Previous Due
                                                Balance
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="number" id="previous_due" class="form-control"
                                                    name="previous_due" placeholder="Due Balance" step="any"
                                                    min="0">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="company_address" class="col-sm-12 col-form-label pr-0">Company
                                                Address
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea name="company_address" class="form-control" id="company_address" cols="10" rows="3"
                                                    placeholder="Company Address"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end p-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection
@section('css_plugins')
    select2
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    select2
    <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js_plugins')
    <script src="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js">
    </script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/select2/js/select2-custom.js"></script>
    select 2
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')
    <script>
        $('#productList').on('scroll', function() {
            if ($('#productList').scrollTop() + $('#productList').innerHeight() >= $('#productList')[0]
                .scrollHeight) {
                productList();
            }
        });
        productList();

        function productList() {
            var pageNo = +$('#pageNo').val();
            var currentPg = pageNo + 1;
            $('#pageNo').val(currentPg);
            $.ajax({
                url: "{{ route('admin.pos.product.get') }}",
                type: "get",
                data: {
                    page: currentPg,
                },
                success: function(response) {
                    $('#productList').append(response)
                },
                error: function(xhr) {}
            });
        }

        function productInfo(product_id) {

            var existsInList = false;
            var productItem = null;

            $('.productItem').each(function() {
                var itemID = $(this).find('.itemID').val();
                if (itemID == product_id) {
                    existsInList = true;
                    productItem = this;
                }
            })


            if (existsInList) {
                let currentItemQTY = +$(productItem).find('.itemQTY').val();

                $(productItem).find('.itemQTY').val(currentItemQTY + 1);
                countTotal();

            } else {
                $.ajax({
                    url: "{{ route('admin.pos.purchase.item.get') }}",
                    type: "get",
                    data: {
                        product_id: product_id,
                    },
                    success: function(response) {
                        $('#orderList').append(response)
                        countTotal();
                    },
                    error: function(xhr) {}
                });
            }

            $('#search').val('');
            $('#productsResults').html('');
        }


        function countTotal() {
            var subTotal = 0;

            $('.productItem').each(function(index) {

                let itemCostPrice = +$(this).find('.itemCostPrice').val();
                let itemQTY = +$(this).find('.itemQTY').val();
                let totalPieces = itemQTY;
                let itemPPPrice = itemCostPrice;


                let itemPurchaseVariant = $(this).find('.itemPurchaseVariant option:selected').val();

                if (itemPurchaseVariant != 'custom') {

                    let itemSellVariantPackSize = +$(this).find('.itemPurchaseVariant option:selected').text();
                    totalPieces = itemQTY * itemSellVariantPackSize;

                    itemPPPrice = itemCostPrice / itemSellVariantPackSize;


                }

                $(this).find('.totalPieces').text(totalPieces);
                // $(this).find('.itemPPPrice').html(priceFormat(itemPPPrice));

                let itemTotalPrice = parseFloat(itemCostPrice) * parseFloat(itemQTY);

                $(this).find('.itemTotalPrice').html(itemTotalPrice);
                subTotal += itemTotalPrice;

            })

            $('#subTotal').html(subTotal);

            $('#hiddenTotalCost').val(subTotal);

            let discountAmount = parseFloat(discountcal());


            let totaPayable = parseFloat(subTotal) - parseFloat(discountAmount);

            let deliveryCharges = $('#deliveryCharges').val();

            totaPayable = parseFloat(totaPayable) + parseFloat(deliveryCharges);

            $('#totaPayable').html(totaPayable);
            $('#hiddenTotalPayable').val(totaPayable);


        }

        function removeItem(data) {
            $(data).closest('tr').remove();
            countTotal();
        }

        function discountcal() {
            let type = +$('#distypeset').val();
            let discountAmount = 0;
            let discountInput = parseFloat($('#discountInput').val());
            let subTotal = parseFloat($('#subTotal').html());
            if (discountInput) {
                if (type == 1) {
                    discountAmount = parseFloat(subTotal * discountInput) / 100;
                }
                if (type == 0) {
                    discountAmount = discountInput;
                }
            }
            $('#discountAmount').html(parseFloat(discountAmount));
            $('#hiddenDiscountAmount').val(parseFloat(discountAmount));

            return discountAmount;


        }

        function itemQTYChange(data, type) {
            let currentItemQTY = parseFloat($(data).parent().find('.itemQTY').val());
            if (type == 'p') {
                $(data).parent().find('.itemQTY').val(currentItemQTY + 1);
            }
            if (type == 's') {
                if (currentItemQTY > 1) {
                    $(data).parent().find('.itemQTY').val(currentItemQTY - 1);
                }
            }
            countTotal();
        }


        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        function searchProduct(data) {
            let product_info = $(data).val();

            $.ajax({
                url: "{{ route('admin.pos.product.src') }}",
                type: "get",
                data: {
                    product_info: product_info,
                },
                success: function(response) {
                    console.log(response)
                    $('#productList').html(response[1])

                    if (response[0] == 1) {
                        productInfo(response[2])
                    }
                    // countTotal();
                },
                error: function(xhr) {}
            });

        }

        function customeradd() {
            $('#customeradd').modal('show')

        }

        $('#customeaddSubmit').on('submit', function(event) {
            event.preventDefault();
            var supplier_name = $('#supplier_name').val();
            var supplier_phone_one = $('#supplier_phone_one').val();
            var supplier_phone_two = $('#supplier_phone_two').val();
            var supplier_email = $('#supplier_email').val();
            var supplier_address = $('#supplier_address').val();
            var company_name = $('#company_name').val();
            var company_phone = $('#company_phone').val();
            var company_email = $('#company_email').val();
            var previous_due = $('#previous_due').val();
            var company_address = $('#company_address').val();

            $.ajax({
                url: "{{ route('admin.supplier.store.form.purchase') }}",
                type: "get",
                data: {
                    supplier_name: supplier_name,
                    supplier_phone_one: supplier_phone_one,
                    supplier_phone_two: supplier_phone_two,
                    supplier_email: supplier_email,
                    supplier_address: supplier_address,
                    company_name: company_name,
                    company_phone: company_phone,
                    company_email: company_email,
                    previous_due: previous_due,
                    company_address: company_address,
                },
                success: function(response) {
                    $('#customeradd').modal('hide');
                    $('#supplier_id').html(response)
                    alert('success')

                },
                error: function(xhr) {}
            });
        })


        $('#paymentshow').on('submit', function(event) {
            $type = $('#submittype').val();
            if ($type == 1) {
                event.preventDefault();
                $('#paymentadd').modal('show')
            }
            if ($type == 2) {

            }

            // let productlist = $('#orderList').html();
            // console.log(productlist)
            // $('#productItemList').html(productlist);
            // let supplier_id = $('#supplier_id').val();
            //
            // $('#supplier_id_set').val(supplier_id)
            // $('#total_discount').val(discountcal())

        })


        // function calculatedue(data) {
        //     $payamount = parseFloat($(data).val());
        //     if (!$payamount) {
        //         $payamount = 0;
        //     }
        //     $payable = parseFloat($('#payableamoutdata').html());
        //     $totaldue = $payable - $payamount;
        //     $('#total_paid_amount').val($payamount);
        //     $('#totaldueamount').html($totaldue);

        // }

        $('#store_pos_payment').on('submit', function(event) {

            event.preventDefault();
            let customer_id = $('#supplier_id_set').val();
            $('#paymentshow').submit();


            // if (customer_id == 1) {
            //     let due = parseFloat($('#totaldueamount').html());
            //     // if (due > 0) {
            //     //     event.preventDefault()
            //     //     alert('Due payment is not allow for walk-in customer')
            //     // } else {
            //     //     $("#store_pos_payment").submit();
            //     // }
            //     $("#store_pos_payment").submit();
            // } else {
            //     $("#store_pos_payment").submit();
            // }
        })

        function submittype(data) {
            $('#submittype').val(data);

        }


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
                onclick="productInfo(${product.id})">
                    <span>${product.name}</span>
                </li>
            `;
                });

                $('#productsResults').html(`<ul class="list-group">${resultHTML}</ul>`);
            } else {
                $('#productsResults').html('<p class="text-danger mt-2">No products found</p>');
            }
        }

        function priceFormat(val) {
            return parseFloat(val).toFixed(2);
        }
    </script>
@endsection
