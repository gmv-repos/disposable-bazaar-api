<h3>{{ $product->name }}</h3>

<label>Select Variant</label>
<select class="form-control productVariant" name="variants">
    @foreach ($product->productVariants as $productVariant)
        <option value="{{ $productVariant->id }}">
            {{ $productVariant->variant->pack_size }} - {{ $productVariant->price }}
        </option>
    @endforeach
</select>

<button class="btn btn-success btn-sm my-3 w-100" onclick="productInfo({{$product->id}})">
    Add To List
</button>