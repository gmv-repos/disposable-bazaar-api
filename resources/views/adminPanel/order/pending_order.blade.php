@extends('adminPanel.layout.layout')
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Order No</th>
                            <th>Phone</th>
                            <th>Total Payable</th>
                            <th>Total Payed</th>
                            <th>Order Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orderList as $key=>$order)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                  # {{$order->order_no}}
                                </td>
                                <td>
                                {{$order->customer->phone}}
                                </td>
                                <td>
                                {{round($order->grand_total)}}
                                </td>
                                <td>
                                @if($order->order_status != 5)
                                {{round($totalamountpaid)}}
                                @elseif($order->order_status == 5)
                                {{round($order->grand_total)}}
                                @endif

                                
                                </td>
                                @if($order->order_status==1)
                                    <td><span class="badge bg-success">Pending</span></td>
                                @elseif($order->order_status==2)
                                    <td>
                                        <span class="badge bg-warning">Processing</span>
                                    </td>
                                @elseif($order->order_status==3)
                                    <td>
                                        <span class="badge bg-primary">On The Way</span>
                                    </td>
                                @elseif($order->order_status==4)
                                <td>
                                    <span class="badge bg-danger">Canceled</span>
                                </td>
                                @elseif($order->order_status==5)
                                <td>
                                    <span class="badge bg-success">Completed</span>
                                </td>
                                @endif

                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Settings
                                        </button>
                                        <ul class="dropdown-menu" style="">
                                            <li><a
                                                    class="dropdown-item"
                                                    href="{{route('admin.order.detail',['order_id'=>$order->id])}}">
                                                    Order Detail</a>
                                            </li>
                                            
                                            @if($order->order_status != 1 && $order->order_status != 5)
                                            <li class="align-items-center"
                                                onclick="return confirm('Are you sure you want to update this order status?');">
                                                <a
                                                    class="dropdown-item"
                                                    href="{{route('admin.order.status.update',['status'=>1,'order_id'=>$order->id])}}">
                                                    Pending</a>
                                            </li>
                                            @endif
                                            @if($order->order_status != 2 && $order->order_status != 5)
                                            <li class="align-items-center"
                                                onclick="return confirm('Are you sure you want to update this order status?');">
                                                <a
                                                    class="dropdown-item"
                                                    href="{{route('admin.order.status.update',['status'=>2,'order_id'=>$order->id])}}">
                                                    Processing</a>
                                            </li>
                                            @endif
                                            @if($order->order_status != 3 && $order->order_status != 5)
                                            <li class="align-items-center"
                                                onclick="return confirm('Are you sure you want to update this order status?');">
                                                <a
                                                    class="dropdown-item"
                                                    href="{{route('admin.order.status.update',['status'=>3,'order_id'=>$order->id])}}">
                                                    On The Way</a>
                                            </li>
                                            @endif
                                            @if($order->order_status != 4 && $order->order_status != 5)
                                                <li class="align-items-center"
                                                    onclick="return confirm('Are you sure you want to update this order status?');">
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{route('admin.order.status.update',['status'=>4,'order_id'=>$order->id])}}">
                                                        Cancel Order</a>
                                                </li>
                                            @endif
                                            @if($order->order_status != 5 )
                                                <li class="align-items-center"
                                                    onclick="return confirm('Are you sure you want to update this order status?');">
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{route('admin.order.status.update',['status'=>5,'order_id'=>$order->id])}}">
                                                        Complete</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                        @endforeach



                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection
@section('css_plugins')
    <link href="{{asset('assets/adminPanel')}}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>
@endsection
@section('js_plugins')

    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
@endsection
@section('js')

    <script>
        $(document).ready(function () {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>

@endsection
