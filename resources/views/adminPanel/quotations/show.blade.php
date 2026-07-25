@extends('adminPanel.layout.layout')

@section('main_content')
    <div class="page-content">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Quotation Details</h4>
            </div>
            <div class="card-body p-4">
                <div class="form-body mt-4">
                    <form action="{{ route('quotations.create.sell', $quotation->id) }}" method="POST" id="quotationForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <b>Customer : {{ $quotation->customer_name ?? 'N/A' }}</b>
                                <br><br>
                                <b>Customer : {{ $quotation->company_name ?? 'N/A' }}</b>
                                <br><br>
                                <b>Valid Until : {{ dateFormat($quotation->valid_until) }}</b>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="table-responsive">
                                    <table class="w-100 table" id="QuotationItemsTable">
                                        <thead>
                                            <tr class="w-100 bg-light text-dark rounded">
                                                <th>Img</th>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Variants</th>
                                                <th>Price Per Piece</th>
                                                <th>QTY</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_discount = 0;
                                                $total_payable_amount = 0;
                                                $total_payable_amount = 0;
                                            @endphp
                                            @foreach ($quotation->quotationItems as $qItem)
                                                <input type="hidden" name="product_id[]" value="{{ $qItem->product_id }}">
                                                <input type="hidden" name="product_variant_price[]"
                                                    value="{{ $qItem->price }}">
                                                <input type="hidden" name="product_variant_id[]"
                                                    value="{{ $qItem->product_variant_id }}">
                                                <input type="hidden" name="brand_id[]" value="{{ $qItem->brand_id }}">
                                                <input type="hidden" name="product_discount[]"
                                                    value="{{ $qItem->discount }}">
                                                <input type="hidden" name="sell_qty[]" value="{{ $qItem->quantity }}">
                                                <input type="hidden" name="product_cost[]" value="{{ $qItem->price }}">

                                                <tr class="bg-light text-dark rounded"
                                                    productID="{{ $qItem->product_id }}">

                                                    <td>
                                                        @if ($qItem?->product?->image_path)
                                                            <img src="{{ asset($qItem->product?->image_path ?? '') }}"
                                                                alt="Product Image" class="img-fluid" width="50px">
                                                        @else
                                                            <img src="{{ asset('assets/images/default.png') }}"
                                                                alt="Product Image" class="img-fluid" width="50px">
                                                        @endif
                                                    </td>
                                                    <td>{{ $qItem->product?->name }}</td>
                                                    <td>{{ $qItem->brand->name ?? 'N/A' }}</td>
                                                    <td>{{ $qItem->productVariant->variant->pack_size ?? 'N/A' }}</td>
                                                    <td class="itemPrice">{{ $qItem->price }}</td>
                                                    <td>
                                                        {{ $qItem->quantity }}
                                                    </td>

                                                    <td class="itemTotalPrice">{{ $qItem->total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>

                                            <input type="hidden" name="total_payable"
                                                value="{{ $quotation->payable_amount }}">
                                            <input type="hidden" name="bank_id" value="">
                                            <input type="hidden" name="discountAmount" value="{{ $quotation->discount }}">
                                            <tr>
                                                <th colspan="5"></th>
                                                <th>Grand Total</th>
                                                <th id="grandTotal">{{ $quotation->payable_amount + $quotation->discount }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="5"></th>
                                                <th>Discount</th>
                                                <th>
                                                    {{ $quotation->discount }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="5"></th>
                                                <th>TAX</th>
                                                <th>
                                                    {{ $quotation->tax }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="5"></th>
                                                <th>Payable Amount</th>
                                                <th>{{ $quotation->payable_amount }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                            <div class="col-md-12 mt-3">

                                <div>{{ $quotation->notes }}</div>
                            </div>
                        </div>

                        @if ($quotation->status == 'Pending')
                            <div class="col-md-12 mt-3 text-center">
                                <button type="submit" class="btn btn-primary w-50">
                                    Create Sell
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
@endsection
