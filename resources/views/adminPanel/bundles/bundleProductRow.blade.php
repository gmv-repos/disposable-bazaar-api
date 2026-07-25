<tr class="bg-light text-dark rounded align-middle" productID="{{ $product->id }}">
    <input type="hidden" name="hiddenProductIDs[]" value="{{ $product->id }}">

    <td>
        <img class="pimgst" src="{{ asset($product->image_path ?: 'assets/adminPanel/images/dummy.png') }}"
            alt="{{ $product->name }}">
    </td>

    <td>{{ $product->name }}</td>

    <td>
        @if ($brands->isNotEmpty())
            <select class="form-control productBrandSelect" name="productBrands[]">
                <option value="">Select Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                @endforeach
            </select>
        @else
            N/A <input type="hidden" class="productBrandSelect" name="productBrands[]" value="">
        @endif
    </td>

    <td>
        @if ($product->productVariants->isNotEmpty())
            <select class="form-control productVariantSelect" name="productVariants[]" onclick="productVariantChange(this)">
                <option value="custom">Custom</option>
                @foreach ($product->productVariants as $productVariant)
                    @php
                        $packSize = $productVariant->variant->pack_size;
                        $totalPrice = $productVariant->price_per_peice * $packSize;
                    @endphp
                    <option value="{{ $productVariant->id }}" vPackSizePrice="{{ $totalPrice }}">
                        {{ $packSize }}
                    </option>
                @endforeach
            </select>
        @else
            N/A <input type="hidden" class="productVariantSelect" name="productVariants[]" value="">
        @endif
    </td>

    <td>
        @if ($product->productLidOptions->isNotEmpty())
            <select class="form-control productLidSelect" name="productLids[]" onchange="productLidChange(this)">
                <option value="">Select Lid</option>
                @foreach ($product->productLidOptions as $lidOption)
                    <option value="{{ $lidOption->id }}" lidPrice="{{ $lidOption->price }}">
                        {{ $lidOption->lidOption->name }}
                    </option>
                @endforeach
            </select>
        @else
            N/A <input type="hidden" class="productLidSelect" name="productLids[]" value="">
        @endif
    </td>

    <td>
        <input type="number" class="form-control productPriceInput" name="productPrices[]" value="1" min="1"
            oninput="calculateTotal()">
    </td>

    <td>
        <input type="number" class="form-control productQtyInput" name="productQTYs[]" value="1" min="1"
            oninput="calculateTotal()">
    </td>

    <td class="productTotalPrice">0</td>

    <td>
        <i class="lni lni-trash" style="cursor: pointer;" onclick="removeItem(this)"></i>
    </td>
</tr>