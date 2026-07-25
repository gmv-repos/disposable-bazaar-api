@php
    $brandList = DB::table('brands')->where('status',1)->get();
@endphp
<tr class="productItem text-center">
    <input type="hidden" name="itemID[]" class="itemID" value="{{ $productinfo->id }}">
    <input type="hidden" class="availableQuantity" value="{{ $productinfo->available_quantity }}">

    <td>
    <img class="pimgst" src="{{ asset($productinfo->image_path ?: 'assets/adminPanel/images/dummy.png') }}" alt="{{$productinfo->name}}">
    </td>
    <td>        
        {{ $productinfo->name }}
    </td>
    <td>
        <select class="form-select form-select-sm itemBrand" name="itemBrand[]">
            @foreach($brandList as $blRow)
            {{-- <option value="{{ $blRow->brand->id }}">
                {{ $blRow->brand->name }}
            </option> --}}
            <option value="{{ $blRow->id }}">
                {{ $blRow->name }}
            </option>
            @endforeach
        </select>
    </td>

    <td>
        <select class="form-select form-select-sm itemSellVariant" name="itemSellVariant[]" onchange="itemVariantChange(this)">
            <option value="custom">Custom</option>
            @foreach($productinfo->productVariants as $productVariant)
                <option 
                    value="{{ $productVariant->id }}" 
                    itemSellVariantPrice="{{ $productVariant->price }}">
                    {{ $productVariant->variant->pack_size }}                    
                </option>
            @endforeach
        </select>        
    </td>

    <td>
        <input class="form-control form-control-sm mb-1 itemSellPrice" name="itemSellPrice[]" oninput="countTotal()" type="number" value="0">
         <small class="itemPPPrice"></small>
    </td>

    <td>
        <div class="d-flex">
            <button type="button" class="btn plussub" onclick="itemQTYChange(this,'p')">+</button>

            <input class="form-control form-control-sm w-25 mx-2 itemQTY" name="itemQTY[]" oninput="itemQtyCheckOnInput(this)" type="text" value="1">

            <button type="button" class="btn plussub" onclick="itemQTYChange(this,'s')">-</button>
        </div>

    </td>

    <td>
        <strong class="totalPieces"></strong>
    </td>

    <td>
        <strong class="itemTotalPrice"></strong>
    </td>

    <td>
        <span onclick="removeItem(this)">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-x text-primary">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </span>
    </td>

</tr>