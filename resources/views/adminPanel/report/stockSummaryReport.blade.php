  @extends('adminPanel.layout.layout')
  @section('main_content')
      <div class="page-content">
          <div class="card">
              <div class="card-body">
                  <div class="table-responsive">
                      <h1>Stock Summary Report</h1>
                      <form action="{{ route('admin.report.stock-summary-report') }}" method="GET">
                          <div class="row justify-content-center align-items-center my-4">
                              <div class="col-sm-2">
                                  <select name="product" class="form-control" id="product">
                                      <option value="">Select Product</option>
                                      @foreach ($allProducts as $product)
                                          <option value="{{ $product->id }}"
                                              {{ request()->product == $product->id ? 'selected' : '' }}>
                                              {{ $product->name }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="col-sm-4 d-flex">
                                  <input type="date" name="startdate" value="{{ request()->startdate }}"
                                      class="form-control">
                                  &nbsp;
                                  <span style="margin-top: 5px;">To</span>
                                  &nbsp;
                                  <input type="date" name="enddate" value="{{ request()->enddate }}"
                                      class="form-control">
                                  &nbsp;
                              </div>
                              <div class="col-sm-3">
                                  <button type="submit" class="btn btn-info w-100">
                                      Search
                                  </button>
                              </div>
                          </div>
                      </form>
                      <hr />
                      {{-- <div class="d-flex justify-content-end">
                          <form method="GET" action="{{ route('admin.report.stock-summary-report') }}" target="_blank">
                              <input type="hidden" name="action" value="pdf">
                              <input type="hidden" name="product" value="{{ $productId }}">
                              <input type="hidden" name="startdate" value="{{ $startdate }}">
                              <input type="hidden" name="endDate" value="{{ $endDate }}">
                              <button type="submit" class="btn btn-secondary">
                                  <i class="lni lni-printer"></i> PDF
                              </button>
                          </form>
                          {!! \App\Helpers\CommonHelper::displayPrintButtonInBlade('printable-stock-summary-report') !!}
                      </div> --}}
                      <div id="printable-stock-summary-report">
                          <table id="example" class="table table-striped table-bordered dtReport">
                              <thead>
                                  <tr>
                                      <th>S.NO.</th>
                                      <th>Product Name</th>
                                      <th>Total Purchase Qty</th>
                                      <th>Total Sell Qty</th>
                                      <th>Stock</th>
                                      <th>No of Cartons</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @php $i = 1; @endphp
                                  @foreach ($data as $dRow)
                                      @php
                                          $stock =
                                              $dRow->total_purchase_qty -
                                              ($dRow->total_sale_qty + $dRow->total_order_qty);
                                      @endphp
                                      <tr>
                                          <td>{{ $i++ }}</td>
                                          <td>{{ $dRow->name }}</td>
                                          <td>{{ $dRow->total_purchase_qty ?? 0 }}</td>
                                          <td>{{ $dRow->total_sale_qty + $dRow->total_order_qty }}</td>
                                          <td>{{ $stock }}</td>
                                          <td>
                                              @if (!empty($dRow->no_of_piece_qty_in_carton) && $dRow->no_of_piece_qty_in_carton != 0)
                                                  {{ $stock / $dRow->no_of_piece_qty_in_carton }}
                                              @else
                                                  0
                                              @endif
                                          </td>
                                      </tr>
                                  @endforeach


                              </tbody>
                          </table>
                      </div>

                  </div>
              </div>
          </div>

      </div>
      <!--end page wrapper -->
  @endsection
  @section('css_plugins')
      <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  @endsection
  @section('js_plugins')
      <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
      <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
  @endsection
  @section('js')
      <script>
          $(document).ready(function() {

              $('#product').select2();
              $('#supplier').select2();
              // $('#example').DataTable({});

              // var table = $('#example').DataTable({
              //     lengthChange: false,
              //     buttons: ['copy', 'excel', 'pdf', 'print']
              // });

              // table.buttons().container()
              //     .appendTo('#example2_wrapper .col-md-6:eq(0)');
          });
      </script>
  @endsection
