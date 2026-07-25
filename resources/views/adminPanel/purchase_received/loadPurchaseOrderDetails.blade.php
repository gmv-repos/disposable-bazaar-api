<form action="{{ route('purchase.received.store') }}" method="POST" id="purchaseReceivedForm">
    @csrf

    <input type="hidden" name="purchaseID" value="{{ $purchase->id }}">
    <input type="hidden" name="supplierID" value="{{ $supplierInfo->id }}">

    <div class="row justify-content-between mt-5 p-3">
        <div class="col-md-6">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <h4>Supplier</h4>
                </li>
                <li class="list-group-item">
                    <b> Name : </b> {{ $supplierInfo->supplier_name }}
                </li>
                <li class="list-group-item">
                    <b> Address : </b> {{ $supplierInfo->supplier_address }}
                </li>
                <li class="list-group-item">
                    <b> Phone # </b> {{ $supplierInfo->supplier_phone_one }}
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <h4>PURCHASE</h4>
                </li>
                <li class="list-group-item">
                    <b> Code # </b> {{ $purchase->purchase_code }}
                </li>
                <li class="list-group-item">
                    <b> Date : </b> {{ date('d-m-Y', strtotime($purchase->date)) }}
                </li>
                <li class="list-group-item">
                </li>
            </ul>
        </div>
        <hr />
    </div>
    <div class="row">
        <div class="col-md-12 mt-5">
            <table class="table-striped table">
                <thead>
                    <tr class="bg-light text-dark py-3">
                        <th style="width: 5%">IMG</th>
                        <th>Product Name</th>
                        {{-- <th>Varient/Custom</th> --}}
                        <th>Cost Price</th>
                        <th>QTY</th>
                        {{-- <th>Total Pieces</th> --}}
                        <th>Total</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseDetails as $item)
                        @php
                            $purchaseReceiveItems->each(function ($prItem) use ($item) {
                                if ($prItem->product_id == $item->product_id) {
                                    $item->total_qty -= $prItem->total_qty;
                                }
                            });
                            if ($item->total_qty <= 0) {
                                continue;
                            }
                        @endphp
                        <tr class="productItem">
                            <input type="hidden" name="listID[]" class="listID" value="{{ $item->id }}">
                            <input type="hidden" name="itemID[]" class="itemID" value="{{ $item->product_id }}">
                            <input type="hidden" name="brandID[]" class="brandID" value="{{ $item->brand_id }}">

                            <td style="width: 5%">
                                <img class="w-100" src="{{ asset($item->productInfo->image_path) }}">
                            </td>

                            <td>
                                {{ $item->productInfo->name }}
                            </td>

                            {{-- <td>
                            @if (is_null($item->product_variant_id))
                            <input type="text" class="form-control form-control-sm w-50 itemPurchaseVariant" name="itemPurchaseVariant[]" value="custom" readonly>
                            @else
                            <input type="hidden" class="itemPurchaseVariant" name="itemPurchaseVariant[]" value="{{ $item->product_variant_id }}">
                            <input type="text" class="form-control form-control-sm w-50 showItemPurchaseVariant" value="{{ $item->productVariant->variant->pack_size }}" readonly>
                            @endif
                        </td> --}}

                            <td>
                                <input class="form-control form-control-sm w-50 itemCostPrice" name="itemCostPrice[]"
                                    oninput="countTotal()" type="number" value="{{ $item->unit_cost }}" readonly>
                                {{-- Per Pieces <small class="itemPPPrice">
                                {{ $item->unit_cost / $item->total_qty }}
                            </small> --}}
                            </td>

                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn plussub"
                                        onclick="itemQTYChange(this,'p')">+</button>

                                    <input class="form-control form-control-sm w-25 itemQTY mx-2" name="itemQTY[]"
                                        oninput="countTotal()" type="text" max="{{ $item->total_qty }}"
                                        value="{{ $item->total_qty }}">

                                    <button type="button" class="btn plussub"
                                        onclick="itemQTYChange(this,'s')">-</button>
                                </div>

                            </td>

                            {{-- <td>
                            <strong class="totalPieces"></strong>
                        </td> --}}

                            <td>
                                <strong class="itemTotalPrice">{{ $item->purchase_payable_amount }}</strong>
                            </td>

                            <td>
                                <span onclick="removeItem(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-x text-primary">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end">
        <input type="hidden" name="hiddenSubTotal" id="hiddenSubTotal">
        <div class="col-md-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-around">
                    <b> SUBTOTAL : </b>
                    <span class="fw-bold" id="subTotal"></span>
                </li>
            </ul>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-md-3">
            <button class="btn btn-success w-100">
                S A V E
            </button>
        </div>
    </div>
</form>
