@extends('adminPanel.layout.layout')

@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Create PR/GRNs</h4>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-select" name="purchase_order_id" id="purchase_order_id"
                                onchange="loadPurchaseOrderDetails()">
                                <option value="">Select PO Number</option>
                                @foreach ($purchases as $purchase)
                                    <option value="{{ $purchase->id }}">{{ $purchase->purchase_code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div id="loadPurchaseOrderDetails">

                </div>

            </div>
        </div>



    </div>
    <!--end page wrapper -->
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            $('#purchase_order_id').select2();
        });


        function itemQTYChange(data, type) {
            let currentItemQTY = parseFloat($(data).parent().find('.itemQTY').val());
            let max = parseFloat($(data).parent().find('.itemQTY').attr('max'));
            if (type == 'p') {
                if (currentItemQTY < max) {
                    $(data).parent().find('.itemQTY').val(currentItemQTY + 1);
                }
            }
            if (type == 's') {
                if (currentItemQTY > 1) {
                    $(data).parent().find('.itemQTY').val(currentItemQTY - 1);
                }
            }
            countTotal();
        }




        function countTotal() {
            var subTotal = 0;

            $('.productItem').each(function(index) {

                let itemCostPrice = +$(this).find('.itemCostPrice').val();
                let itemQTY = +$(this).find('.itemQTY').val();
                let totalPieces = itemQTY;
                let itemPPPrice = itemCostPrice;


                let itemPurchaseVariant = $(this).find('.itemPurchaseVariant').val();

                if (itemPurchaseVariant != 'custom') {

                    let itemPurchaseVariantPackSize = +$(this).find('.showItemPurchaseVariant').val();

                    totalPieces = itemQTY * itemPurchaseVariantPackSize;

                    itemPPPrice = itemCostPrice / itemPurchaseVariantPackSize;


                }

                // $(this).find('.totalPieces').text(totalPieces);
                // $(this).find('.itemPPPrice').html(priceFormat(itemPPPrice));

                let itemTotalPrice = parseFloat(itemCostPrice) * parseFloat(itemQTY);

                $(this).find('.itemTotalPrice').html(priceFormat(itemTotalPrice));
                subTotal += itemTotalPrice;

            })

            $('#subTotal').html(priceFormat(subTotal));

            $('#hiddenSubTotal').val(subTotal);

            // let discountAmount = parseFloat(discountcal());
            let discountAmount = 0;


            let totaPayable = parseFloat(subTotal) - parseFloat(discountAmount);

            let deliveryCharges = $('#deliveryCharges').val();

            totaPayable = parseFloat(totaPayable) + parseFloat(deliveryCharges);

            $('#totaPayable').html(priceFormat(totaPayable));
            $('#hiddenTotalPayable').val(totaPayable);


        }


        function removeItem(data) {
            var productItemCount = $('tr.productItem').length;

            if (productItemCount > 1) {
                $(data).closest('tr').remove();
                countTotal();
            } else {
                Toastify({
                    text: 'Atleaset one item required',
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#FF5F6D",
                }).showToast();
                return false;
            }
        }


        function priceFormat(val) {
            return parseFloat(val).toFixed(2);
        }

        function loadPurchaseOrderDetails() {
            $('#loadPurchaseOrderDetails').html('');
            var purchaseId = $('#purchase_order_id').val();
            $.ajax({
                url: '{{ route('purchase.received.loadPurchaseOrderDetails') }}',
                type: 'GET',
                data: {
                    purchase_id: purchaseId
                },
                success: function(response) {
                    $('#loadPurchaseOrderDetails').html(response);
                    countTotal();
                }
            });
        }
    </script>
@endsection
