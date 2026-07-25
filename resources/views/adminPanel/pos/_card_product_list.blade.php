@foreach($productList as $product)
<div class="col-sm-3 mt-2" >
    <div class="card border-primary border-bottom border-3 border-0 poscard" onclick="productInfo({{$product->id}})">
        <div class="d-flex justify-content-center imagediv">
        <img src="{{ asset($product->image_path ?: 'assets/adminPanel/images/dummy.png') }}" class="card-img-top productImagestyle" alt="{{$product->name}}">
        </div>
        <div class="card-body posProductCard">
        <!-- productNameHeight -->
            <p class="card-text">
                {{ strlen($product->name)>60 ? substr($product->name,0,60):$product->name}}
            </p>

            <p class="card-text">
                Brand : {{ $product->brand->name ?? 'Unknown' }}
            </p>
            {{-- <div class="d-flex align-items-center btopst gap-2">
                <span class="productpricecolor">{{$product->current_sale_price }}</span>
            </div> --}}
        </div>
    </div>
</div>
@endforeach
