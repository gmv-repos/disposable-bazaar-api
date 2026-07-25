@foreach ($salesOrder->items as $salesOrderItem)

    @php
        $productinfo = $salesOrderItem->product;
        $pack_size_qty = $salesOrderItem->itemVariant->variant->pack_size ?? 0;

        $brandList = \App\Models\Stock::with('brand')
            ->where('product_id', $productinfo->id)
            ->get();
    @endphp

    @if (
            $salesOrderItem->delivered_quantity >= $salesOrderItem->quantity || 
             $pack_size_qty > $productinfo->available_quantity
        )
        @continue
    @endif

   

    <tr class="productItem text-center">
        <input type="hidden" name="salesOrderItemIDs[]" value="{{ $salesOrderItem->id }}">
        <input type="hidden" name="itemID[]" class="itemID" value="{{ $productinfo->id }}">
        <input type="hidden" class="availableQuantity" value="{{ $productinfo->available_quantity }}">

        <td>
            <img class="pimgst" src="{{ asset($productinfo->image_path ?: 'assets/adminPanel/images/dummy.png') }}"
                alt="{{$productinfo->name}}">
        </td>
        <td>
            {{ $productinfo->name }}
        </td>
        <td>
            <select class="form-select form-select-sm itemBrand" name="itemBrand[]">
                @foreach($brandList as $blRow)
                    <option value="{{ $blRow->brand->id }}" {{ $salesOrderItem->brand_id == $blRow->brand->id ? 'selected' : '' }}>
                        {{ $blRow->brand->name }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <select class="form-select form-select-sm itemSellVariant" name="itemSellVariant[]"
                onchange="itemVariantChange(this)">
                <option value="custom">Custom</option>
                @foreach($productinfo->productVariants as $productVariant)                   
                    <option value="{{ $productVariant->id }}" itemSellVariantPrice="{{ $productVariant->price }}"
                        @if($productVariant->variant->pack_size > $productinfo->available_quantity) disabled @endif
                        {{ $salesOrderItem->product_variant_id ? 'selected' : '' }}>
                        {{ $productVariant->variant->pack_size }}
                    </option>
                @endforeach
            </select>

        </td>

        <td>
            <input class="form-control form-control-sm mb-1 itemSellPrice" name="itemSellPrice[]" oninput="countTotal()"
                type="number" value="{{ $salesOrderItem->sell_price  }}">
            <small class="itemPPPrice"></small>
        </td>

        <td>
            <div class="d-flex">
                <button type="button" class="btn plussub" onclick="itemQTYChange(this,'p')">+</button>
                @php
                   $remainingQTY = $salesOrderItem->quantity - $salesOrderItem->delivered_quantity;
                @endphp
                <input class="form-control form-control-sm w-25 mx-2 itemQTY"  value="{{ $remainingQTY }}" name="itemQTY[]"
                    oninput="itemQtyCheckOnInput(this)" type="text" value="1">

                <button type="button" class="btn plussub" onclick="itemQTYChange(this,'s')">-</button>
            </div>

        </td>
        <td>
            {{ $salesOrderItem->quantity }}
        </td>
        <td>
            {{ $salesOrderItem->delivered_quantity }}
        </td>

        <td class="d-none">
            <strong class="totalPieces"></strong>
        </td>

        <td>
            <strong class="itemTotalPrice"></strong>
        </td>

        <td>
            <span onclick="removeItem(this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-x text-primary">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </span>
        </td>
    </tr>
@endforeach