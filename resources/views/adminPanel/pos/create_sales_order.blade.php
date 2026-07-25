@extends('adminPanel.layout.layout')
@section('main_content')
    <div class="page-content">
        <input type="hidden" id="pageNo" value="0">
        <input type="hidden" id="submittype" value="1">
        <div class="row">
            <div class="col-sm-12 mt-2" style="padding: 0px;">
                <form action="{{route('admin.pos.storeSalesOrder')}}" method="post" id="paymentshow">
                    @csrf
                    <div class="rightpos">
                        <div class="row d-flex justify-content-center align-items-center posTopbar">
                            <div class="col-sm-4">
                                <input type="hidden" name="bank_id" id="set_bank_id">
                                <select name="customer_id" id="customerlist" class="form-control w-100 select2"
                                    onchange="getCustomerInfoHTML(this.value)">
                                    @foreach($posCustomerList as $customer)
                                        <option value="{{$customer->id}}" deliveryCharges="{{ $customer->delivery_charges }}">
                                            {{$customer->name}}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-white">SALE ORDER</h3>
                            </div>
                            <div class="col-sm-4 d-flex justify-content-end">
                                <span class="addCustomer" onclick="customeradd()"><i style="font-size: 18px;"
                                        class="lni lni-circle-plus"></i> &nbsp; Customer</span>
                            </div>
                        </div>
                        <div id="customerInfoHTML">
                            {{-- AJAX Rendered Customer Info --}}
                        </div>

                        <table class="table w-100" id="posSaleTable">
                            <thead>
                                <tr class="bg-light text-dark py-3">
                                    <th>IMG</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>PackSize/Custom</th>
                                    <th>Price</th>
                                    <th>QTY</th>
                                    <th>Total Pieces</th>
                                    <th>Total</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody id="orderList">

                            </tbody>
                        </table>
                        <input type="hidden" name="total_payable" id="total_payable" class="duepayinput form-control">

                        <div>
                            <div class="row justify-content-end align-items-center mb-2">
                                <div class="col-2">
                                    <span>Subtotal Total</span>
                                </div>
                                <div class="col-2">
                                    <strong id="subTotal">00</strong>
                                    <input type="hidden" name="hiddenTotalCost" id="hiddenTotalCost">
                                </div>
                            </div>

                            <div class="row d-flex justify-content-center mt-4">
                                <div class="col-sm-3 d-flex justify-content-center">
                                    <button type="submit" onclick="submittype(2)" class="addCustomer">Submit </button>
                                </div>

                            </div>

                        </div>

                    </div>
                </form>
            </div>

            <div class="col-md-12 mb-3 mt-2 bg-secondary py-2 rounded">
                <h3 class="text-white ps-3">Add Product</h3>
                <div class="leftpos">
                    <input type="text" class="form-control" name="search" id="search"
                        placeholder="Search Product or Brand Name" oninput="searchProductByNameOrBrand(this)">
                    <div id="productsResults"></div>
                </div>
            </div>

        </div>


        {{-- customer create--}}

        <div class="modal fade" id="customeradd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="" id="customeaddSubmit">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12" style="border-right:1px solid #dfdada">
                                    <div class="mb-2 row">
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12  pr-0 col-form-label">Name
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputname" class="form-control" name="name"
                                                    placeholder="Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12  pr-0 col-form-label">Phone
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputphone" class="form-control" name="phone"
                                                    placeholder="Phone" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputname" class="col-sm-12  pr-0 col-form-label">Email
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputemail" class="form-control" name="email"
                                                    placeholder="email">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="customer_area_id" class="col-sm-12  pr-0 col-form-label">
                                                Area
                                            </label>
                                            <div class="col-sm-12">
                                                <select name="customer_area_id" id="customer_area_id" class="form-select">
                                                    @foreach ($areaList as $area)
                                                        <option value="{{ $area->id }}">
                                                            {{ $area->area_name }} - {{ $area->shipping_rate }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <label for="supplier_address" class="col-sm-12  pr-0 col-form-label">
                                                Address
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea name="supplier_address" class="form-control" id="supplier_address"
                                                    cols="10" rows="3" placeholder="Address"></textarea>
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

        {{-- customer create--}}

        {{-- payment--}}
        <div class="modal fade" id="paymentadd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="" id="store_pos_payment">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content modalpay">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12" style="border-right:1px solid #dfdada">
                                    <div class="mb-2 row">
                                        <div class="col-6"><span>Bank Account</span></div>
                                        <div class="col-6 payitem">
                                            <select id="bank_id" class="form-select">
                                                @foreach($bankList as $bank)
                                                    <option value="{{$bank->id}}">{{$bank->bank_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-6"><span>Total Payable Amount</span></div>
                                        <div class="col-6 payitem"><span id="payableamoutdata">00</span></div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-6"><span>Paid Amount</span></div>
                                        <div class="col-6 payitem">
                                            <input type="text" name="" id="totalmaymentamount" oninput="calculatedue(this)"
                                                class="duepayinput form-control">
                                            {{-- <input type="hidden" name="total_payable" id="total_payable"
                                                oninput="calculatedue(this)" class="duepayinput form-control">--}}
                                        </div>
                                    </div>
                                    <div class="mb-2 row duerow">
                                        <div class="col-6"><span>Due Amount</span></div>
                                        <div class="col-6 payitem"><span id="totaldueamount">00</span></div>
                                    </div>
                                </div>
                                <div id="productItemList" style="display: none">

                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end p-3">
                            <button type="submit" onclick="submittype(2)" class="btn btn-primary">Pay Now</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        {{-- payment--}}



    </div>






@endsection
@section('css_plugins')
    {{-- select2--}}
    <link rel="stylesheet"
        href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- select2--}}
    <link href="{{asset('assets/adminPanel')}}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('js_plugins')
    <script
        src="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/select2/js/select2-custom.js"></script>
    {{--select 2--}}
    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')
    <script>
        $('#productList').on('scroll', function () {
            if ($('#productList').scrollTop() + $('#productList').innerHeight() >= $('#productList')[0].scrollHeight) {
                productList();
            }
        });

        productList();

        function productList() {
            var pageNo = +$('#pageNo').val();
            var currentPg = pageNo + 1;
            $('#pageNo').val(currentPg);
            $.ajax({
                url: "{{route('admin.pos.product.get')}}",
                type: "get",
                data: {
                    page: currentPg,
                },
                success: function (response) {
                    $('#productList').append(response)
                },
                error: function (xhr) { }
            });
        }


        function productInfo(product_id) {

            var isstay = 0;
            var selectItem = 0;
            $('.item_product_id').each(function () {
                if (product_id == $(this).val()) {
                    isstay = 1;
                    selectItem = this;
                }
            })
            if (isstay) {
                let qty = +$(selectItem).parent().find('.sellqty').val();
                $(selectItem).parent().find('.sellqty').val(qty + 1);

                countTotal();

            } else {
                $.ajax({
                    url: "{{ route('admin.pos.sell.item.get') }}",
                    type: "get",
                    data: {
                        product_id: product_id,
                    },
                    success: function (response) {

                        if (response.error) {
                            Toastify({
                                text: response.error,
                                duration: 3000,
                                newWindow: true,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                            }).showToast();
                        } else {
                            $('#orderList').append(response)
                            countTotal();
                        }

                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
            }

            $('#search').val('');
            $('#productsResults').html('');
        }

        function removeItem(data) {
            $(data).parent().remove();
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
            $('#hiddenDiscountAmount').val(parseFloat(discountAmount));

            return discountAmount;
        }


        function plssub(data, type) {
            let sallqty = parseFloat($(data).parent().find('.sellqty').val());
            if (type == 'p') {
                var availableQty = $(data).closest('.product_item').find('.availableQuantity').val();
                if (availableQty < sallqty + 1) {
                    Toastify({
                        text: 'Out of Stock',
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                    }).showToast();
                    return false;
                }
                $(data).parent().find('.sellqty').val(sallqty + 1);
            }
            if (type == 's') {
                $(data).parent().find('.sellqty').val(sallqty - 1);
            }
            countTotal()
        }

        $('.select2').select2();


        function searchProduct(data) {
            let product_info = $(data).val();

            $.ajax({
                url: "{{route('admin.pos.product.src')}}",
                type: "get",
                data: {
                    product_info: product_info,
                },
                success: function (response) {
                    console.log(response)
                    $('#productList').html(response[1])

                    if (response[0] == 1) {
                        productInfo(response[2])
                    }
                    // countTotal();
                },
                error: function (xhr) { }
            });

        }

        function customeradd() {
            $('#customeradd').modal('show')

        }

        $('#customeaddSubmit').on('submit', function (event) {
            event.preventDefault();
            var name = $('#inputname').val();
            var phone = $('#inputphone').val();
            var email = $('#inputemail').val();
            var customer_area_id = $('#customer_area_id').val();
            var address = $('#supplier_address').val();

            $.ajax({
                url: "{{route('admin.pos.customer.add.in-pos')}}",
                type: "get",
                data: {
                    name: name,
                    phone: phone,
                    email: email,
                    customer_area_id: customer_area_id,
                    address: address,
                },
                success: function (response) {
                    $('#customeradd').modal('hide');
                    $('#customerlist').html(response)
                    alert('success')

                },
                error: function (xhr) { }
            });
        })


        $('#paymentshow').on('submit', function (event) {

            $type = $('#submittype').val();
            if ($type == 1) {
                event.preventDefault();
                $('#paymentadd').modal('show')
            }
            if ($type == 2) {
                var customer_id = $('#customerlist').val();
                if (customer_id == 1) {
                    let due = parseFloat($('#totaldueamount').html());
                    // if(due>0){
                    //     event.preventDefault();
                    //     alert('Due payment is not allow for walk-in customer')
                    // }
                }
            }
        })


        function calculatedue(data) {
            $payamount = parseFloat($(data).val());
            if (!$payamount) {
                $payamount = 0;
            }
            $payable = parseFloat($('#payableamoutdata').html());
            $totaldue = $payable - $payamount;
            $('#totaldueamount').html($totaldue);
            // $('#totalpayment').val($payamount);

        }

        $('#store_pos_payment').on('submit', function (event) {
            event.preventDefault();
            let bank_id = $('#bank_id').val();
            $('#set_bank_id').val(bank_id);
            $("#paymentshow").submit();
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
                success: function (response) {
                    displaySearchResults(response.searchResult);
                }
            });
        }

        function displaySearchResults(searchResult) {
            if (searchResult && searchResult.length > 0) {
                let resultHTML = '';
                searchResult.forEach(function (product) {
                    resultHTML += `<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                 style="cursor: pointer;" 
                                 onclick="productInfo(${product.id})">
                                 <span>${product.name}</span>
                            </li>`;
                });

                $('#productsResults').html(`<ul class="list-group">${resultHTML}</ul>`);
            } else {
                $('#productsResults').html('<p class="text-danger mt-2">No products found</p>');
            }
        }

        ///New
        function itemQTYChange(data, type) {
            let currentItemQTY = +$(data).parent().find('.itemQTY').val();
            if (type == 'p') {
                var availableQty = $(data).closest('.productItem').find('.availableQuantity').val();
                var itemSellVariant = $(data).closest('.productItem').find('.itemSellVariant').val();
                if (itemSellVariant != 'custom') {
                    var itemSellVariantPackSize = +$(data).closest('.productItem').find('.itemSellVariant option:selected').text();
                    availableQty = Math.floor(availableQty / itemSellVariantPackSize);
                    console.log(currentItemQTY, "X", availableQty);
                }
                if (currentItemQTY >= availableQty) {
                    Toastify({
                        text: 'Out of Stock',
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                    }).showToast();
                    return false;
                }
                $(data).parent().find('.itemQTY').val(currentItemQTY + 1);
            }
            if (type == 's') {
                if (currentItemQTY > 1) {
                    $(data).parent().find('.itemQTY').val(currentItemQTY - 1);
                }
            }
            countTotal();
        }



        function itemQtyCheckOnInput(data) {
            var availableQty = +$(data).closest('.productItem').find('.availableQuantity').val();

            var itemSellVariant = $(data).closest('.productItem').find('.itemSellVariant').val();
            if (itemSellVariant != 'custom') {
                var itemSellVariantPackSize = +$(data).closest('.productItem').find('.itemSellVariant option:selected').text();
                availableQty = Math.floor(availableQty / itemSellVariantPackSize);                
            }
            if (availableQty <= data.value) {
                data.value = parseInt(availableQty);

                Toastify({
                    text: 'Out of stock only ' + parseInt(availableQty) + ' items are left',
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                }).showToast();
                return false;
            }

            countTotal();

        }

        function itemVariantChange(data) {
            let itemSellVariantPrice = +$(data).find('option:selected').attr('itemSellVariantPrice');
            $(data).closest('tr').find('.itemSellPrice').val(itemSellVariantPrice);
            $(data).closest('tr').find('.itemQTY').val(1);
            countTotal();
        }


        function invoiceAreaChange(data) {
            let shippingCost = +$(data).val() || 0;
            $('#shippingCost').val(shippingCost);
            countTotal();
        }

        function countTotal() {
            var subTotal = 0;

            $('.productItem').each(function (index) {

                let itemSellPrice = +$(this).find('.itemSellPrice').val();
                let itemQTY = +$(this).find('.itemQTY').val();

                let itemTotalPrice = parseFloat(itemSellPrice) * parseFloat(itemQTY);

                let itemSellVariant = $(this).find('.itemSellVariant option:selected').val();
                let totalPieces = itemQTY;

                let itemPPPrice = itemSellPrice;

                if (itemSellVariant != 'custom') {

                    // let itemSellVariantPrice = +$(this).find('.itemSellVariant :selected').attr('itemSellVariantPrice');
                    // $(this).find('.itemSellPrice').val(itemSellVariantPrice);

                    // itemSellPrice = itemSellVariantPrice;

                    let itemSellVariantPackSize = +$(this).find('.itemSellVariant option:selected').text();
                    itemPPPrice = itemSellPrice / itemSellVariantPackSize;
                    totalPieces = itemQTY * itemSellVariantPackSize;
                }

                $(this).find('.itemTotalPrice').html(priceFormat(itemTotalPrice));
                $(this).find('.itemPPPrice').html(priceFormat(itemPPPrice));
                $(this).find('.totalPieces').text(totalPieces);
                subTotal += itemTotalPrice;

            })

            $('#subTotal').html(subTotal);

            $('#hiddenTotalCost').val(subTotal);

            let discountAmount = parseFloat(discountcal());
            let totaPayable = parseFloat(subTotal) - parseFloat(discountAmount);
            let shippingCost = +$('#shippingCost').val() || 0;
            totaPayable += shippingCost;

            // let additionalCharges = $('#additionalCharges').val() || 0;
            // totaPayable = parseFloat(totaPayable) + parseFloat(additionalCharges);

            $('#totaPayable').html(totaPayable);
            $('#hiddenTotalPayable').val(totaPayable);
        }

        function priceFormat(val) {
            return parseFloat(val).toFixed(2);
        }


        function getCustomerInfoHTML(customer_id) {
            $.ajax({
                url: "{{ route('admin.pos.getCustomerInfoHTML') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    customer_id: customer_id
                },
                success: function (response) {
                    $('#customerInfoHTML').html(response);
                    countTotal();
                }
            });
        }
    </script>

@endsection