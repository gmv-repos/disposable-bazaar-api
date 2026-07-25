@php
    $brandList = DB::table('brands')->where('status',1)->get();
@endphp
<tr class="productItem">
    <input type="hidden" name="itemID[]" class="itemID" value="{{ $productinfo->id }}">

    <td style="width: 5%">
        <img class="w-100" src="{{asset($productinfo->image_path)}}">
    </td>

    <td>
        {{ $productinfo->name }}
    </td>
    <td>
        <select class="form-select form-select-sm brandList" name="brandListId[]">
            @foreach($brandList as $bRow)
            <option value="{{ $bRow->id }}">
                {{ $bRow->name }}
            </option>
            @endforeach
        </select>
    </td>

    <td class="d-none">
        <select class="form-select form-select-sm itemPurchaseVariant" name="itemPurchaseVariant[]" onchange="countTotal()">
            <option value="custom">Custom</option>
            @foreach($productinfo->productVariants as $productVariant)
            <option value="{{ $productVariant->id }}">
                {{ $productVariant->variant->pack_size }}
            </option>
            @endforeach
        </select>
    </td>

    <td>
        <input class="form-control form-control-sm w-50 itemCostPrice" name="itemCostPrice[]" oninput="countTotal()" type="number" value="0">
        <small class="itemPPPrice"></small>
    </td>

    <td>
        <div class="d-flex">
            <button type="button" class="btn plussub" onclick="itemQTYChange(this,'p')">+</button>

            <input class="form-control form-control-sm w-25 mx-2 itemQTY" name="itemQTY[]" oninput="countTotal()" type="text" value="1">

            <button type="button" class="btn plussub" onclick="itemQTYChange(this,'s')">-</button>
        </div>

    </td>

    {{-- <td>
        <strong class="totalPieces"></strong>
    </td> --}}

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