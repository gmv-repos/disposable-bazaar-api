@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <div>
            <div class="row">
                <div class="col-sm-6">
                    <h1>Order Details</h1>
                </div>
                <div class="col-sm-3">
                </div>
                <div class="col-sm-3">
                    <a href="{{route('admin.ecommerce.order.list')}}">

                        <div class="menu-title">
                            <spna class="add-menu-sidebar" style="display: flex;justify-content: center;align-items: center"
                                data-toggle="modal" data-target="#addOrderModalside">
                                <span class="nav-text text-center text-white"><i class="lni lni-circle-plus"></i>Product
                                    List</span>
                            </spna>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <h5>Shipping Address</h5>
                    <ul class="shipping shipping__st">
                        <li>Area Name : {{$orderList->orderBilling->area->area_name}}</li>
                        <li>Billing Address : {{$orderList->orderBilling->address}}</li>
                        <li>Phone : {{$orderList->phone}}</li>
                        <li>Email : {{$orderList->email}}</li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <h5 class="textRight  mrgst">Order Date</h5>
                    <div class="col-sm-12 tx-al-rt mb-3">
                        <strong>
                            Date:{{ date('d-M-Y', strtotime($orderList->order_date))}}

                        </strong>

                    </div>
                </div>
                {{-- <div class="col-sm-6 tx-al-rt">--}}
                    {{-- <h5>Billing Address</h5>--}}
                    {{-- <ul class="billing__st">--}}
                        {{-- <li>{{$orderList->orderAddress->billing_first_name}}
                            {{$orderList->orderAddress->billing_last_name}}</li>--}}
                        {{-- <li>{{$orderList->orderAddress->billing_address}}</li>--}}
                        {{-- <li>Phone:{{$orderList->orderAddress->billing_phone}}</li>--}}
                        {{-- <li>Email:{{$orderList->orderAddress->billing_email}}</li>--}}
                        {{-- </ul>--}}
                    {{-- </div>--}}
            </div>
            <div class="col-sm-12">

                <table class="table table-responsive" border="8">
                    <thead class="bgclset">
                        <tr>
                            <th class="imgthst">S.NO.</th>
                            <th class="imgthst">IMG</th>
                            <th class="itemNamePrc">Name</th>
                            <th class="itemNamePrc">PACK SIZE</th>
                            <th class="itemprc">QTY</th>
                            <th class="itemtotalprc">TOTAL</th>
                            <th>Customization</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($orderList->orderDetails as $key => $item)

                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><img class="item_img_st" src="{{asset($item->product->image_path)}}" alt=""> </td>
                                <td>{{$item->product->name}}</td>
                                <td>{{round($item->pack_size)}}</td>
                                <td>{{round($item->qty)}}</td>
                                <td class="tx-al-rt ">{{round($item->product_sub_total)}}</td>
                                <td>
                                    @if ($item->is_customize == 1)
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="showModal('{{ $item->additional_customization }}', '{{ asset('storage/app/public/' . $item->customize_logo_image) }}')">
                                            <i class="lni lni-eye"></i>
                                        </button>
                                    @else
                                        <span>'N/A'</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <b>N/A</b>
                                </td>
                            </tr>
                        @endforelse
                        @if(!is_null($orderList->bundle_ids))
                            <tr>
                                <td colspan="9" class="text-center text-white bg-secondary">
                                    <b>
                                        Bundles
                                    </b>
                                </td>
                            </tr>
                            @php
                                $bundleData = json_decode($orderList->bundle_ids, true);
                                $bundleIds = array_column($bundleData, 'id');
                                $bundles = \App\Models\Bundle::whereIn('id', $bundleIds)->get();
                            @endphp

                            @foreach ($bundles as $bun)
                                @php
                                    $matchedBundle = collect($bundleData)->firstWhere('id', $bun->id);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td colspan="2">{{ $bun->reference_code }}</td>
                                    <td colspan="2">{{ $bun->name }}</td>
                                    <td colspan="2">{{ $matchedBundle['qty'] ?? 'N/A' }}</td>
                                    <td colspan="2">{{ $bun->payable_amount * $matchedBundle['qty'] }}</td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="5"></td>
                            <td class="tx-al-rt">SUBTOTAL</td>
                            <td class="tx-al-rt">{{round($orderList->total_amount)}}</td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="tx-al-rt"> SHIPPING RATE</td>
                            <td class="tx-al-rt">{{round($orderList->shipping_charges)}}</td>
                        </tr>
                        <tr class="border__topst">
                            <td colspan="5"></td>
                            <td class="tx-al-rt">TOTAL</td>
                            <td class="tx-al-rt"> <strong>{{round($orderList->grand_total)}}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end page wrapper -->


    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Item Customize</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="border rounded p-3 mb-2 w-100" id="itemDetails"></div>
                    <img id="itemImage" src="" alt="Customized Logo" class="img-fluid w-100">
                </div>
                <div class="modal-footer justify-content-between">
                    <a href="" class="btn btn-success btn-sm" id="itemLogoDownload" download>
                        <i class="lni lni-download"></i>
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        function showModal(details, logo) {
            $('#itemDetails').text(details);
            $('#itemImage').attr('src', logo);
            $('#itemLogoDownload').attr('href', logo);
            $('#itemModal').modal('show');
        }

        $('.close, [data-dismiss="modal"]').on('click', function () {
            $('#itemDetails').text('');
            $('#itemImage').attr('src', '');
            $('#itemLogoDownload').attr('href', '');
            $('#itemModal').modal('hide');
        });
    </script>

@endsection