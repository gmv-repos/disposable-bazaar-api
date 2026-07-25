@extends('adminPanel.layout.layout')
@section('css')
    <style>
        img {
            display: block;
            max-width: 100%;
        }

        .preview {
            overflow: hidden;
            width: 160px;
            height: 160px;
            margin: 10px;
            border: 1px solid red;
        }
    </style>
@endsection
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <!-- Product Variant Export -->
                    <form action="{{ route('product.productVariantsExport') }}" method="GET" id="variantExportForm"
                        class="d-inline">
                        <input type="hidden" name="product_ids" id="variant_product_ids">
                        <button type="button" class="btn btn-success btn-sn" onclick="submitVariantExport()">
                            Export Variants
                        </button>
                    </form>

                    <!-- Product Variant Import -->
                    <button type="button" class="btn btn-warning btn-sn" data-bs-toggle="modal"
                        data-bs-target="#variantImportModal">
                        Import Variants
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sn" data-bs-toggle="modal"
                        data-bs-target="#exportModal">
                        Export Excel
                    </button>
                    <button type="button" class="btn btn-primary btn-sn" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        Import Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.product.export') }}" method="POST" id="productListForm">
                    @csrf
                    <input type="hidden" id="selectimgdiv">
                    <div class="table-responsive">
                        <table id="example" class="table-striped table-bordered table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>--</th>
                                    <th>S.NO.</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>SKU Code</th>
                                    {{-- <th>Category</th> --}}
                                    {{-- <th>Subcategory</th> --}}
                                    {{-- <th>Sell Price</th>
                                    <th>WholeSell Price</th> --}}
                                    <th>Available</th>
                                    <th>Stock Status</th>
                                    <th>Is Customizable</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productList as $key => $product)
                                    <tr>
                                        <td>
                                            <input class="form-check-input" type="checkbox" value="{{ $product->id }}"
                                                name="productsToExport[]">
                                        </td>
                                        <td>{{ $product->serial_no ?? 'N/A' }}</td>
                                        <td>
                                            <img class="productImglistst"
                                                src="{{ asset($product->image_path ?: 'assets/adminPanel/images/dummy.png') }}"
                                                alt="{{ $product->name }}">
                                        </td>
                                        <td>

                                            {{ $product->name }}
                                        </td>
                                        <td>
                                            {{ $product->code }}
                                        </td>
                                        {{-- <td> --}}
                                        {{-- {{$product->productCategory->name}} --}}
                                        {{-- </td> --}}
                                        {{-- <td> --}}
                                        {{-- {{$product->productSubcategory->name}} --}}
                                        {{-- </td> --}}

                                        {{-- <td>{{$product->current_sale_price}}</td>
                                        <td>{{$product->current_wholesale_price}}</td> --}}
                                        <td>{{ $product->available_quantity }}</td>

                                        <td class="toggle-stock-status text-center" data-product-id="{{ $product->id }}">
                                            @if ($product->stock_status == 1)
                                                <span class="badge bg-primary" style="cursor: pointer;">In Stock</span>
                                            @else
                                                <span class="badge bg-warning" style="cursor: pointer;">Out of Stock</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($product->is_customizeable)
                                                <span class="badge bg-success px-3">Yes</span>
                                            @else
                                                <span class="badge bg-secondary px-3">No</span>
                                            @endif
                                        </td>

                                        @if ($product->status == 1 && $product->deleted == 0)
                                            <td><span class="badge bg-success">Active</span></td>
                                        @else
                                            <td>
                                                <span class="badge bg-danger">Inactive</span>
                                            </td>
                                        @endif

                                        <td>
                                            <div class="dropdown d-flex justify-content-center">
                                                <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                    @if ($product->status == 1 && $product->deleted == 0)
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.product.detail', ['product_id' => $product->id]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-edit text-primary">
                                                                    <path
                                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                                    </path>
                                                                    <path
                                                                        d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                                    </path>
                                                                </svg>
                                                                View</a>
                                                        </li>
                                                        <li onclick="barcodePrint( {{ $product->id }})"
                                                            style="cursor: pointer">
                                                            <span class="dropdown-item" href="">
                                                                <i class="lni lni-printer"
                                                                    style="    font-size: 18px;color: #008cff;"></i>
                                                                Barcode Print
                                                            </span>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.product.edit', ['product_id' => $product->id]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-edit text-primary">
                                                                    <path
                                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                                    </path>
                                                                    <path
                                                                        d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                                    </path>
                                                                </svg>
                                                                Edit</a>
                                                        </li>
                                                        <li class="align-items-center"
                                                            onclick="return confirm('Are you sure you want to delete this item?');">
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.delete.product', ['id' => $product->id]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-trash text-primary">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                </svg>
                                                                Delete</a>
                                                        </li>
                                                    @else
                                                        <li class="align-items-center"
                                                            onclick="return confirm('Are you sure you want to restore this item?');">
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.restore.product', ['id' => $product->id]) }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-trash text-primary">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                </svg>
                                                                Restore</a>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                {{-- <tr> --}}
                                {{-- <th colspan="6"></th> --}}
                                {{-- <th>Salary</th> --}}
                                {{-- </tr> --}}
                            </tfoot>
                        </table>
                    </div>

                    {{-- Export Columns Selection Modal --}}
                    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog"
                        aria-labelledby="exportModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Select Columns to Export</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @php
                                        $columns = [
                                            'name' => 'Product Name',
                                            'slug' => 'Slug',
                                            'category_id' => 'Category Name',
                                            'subcategory_id' => 'Subcategory Name',
                                            'image_path' => 'Image URL',
                                            'code' => 'SKU Code',
                                            'brand_id' => 'Brand Name',
                                            'available_quantity' => 'Available Quantity',
                                            'is_popular' => 'Is Popular',
                                            'is_trending' => 'Is Trending',
                                            'additional_information' => 'Additional Information',
                                            'status' => 'Status',
                                            'stock_alert' => 'Stock Alert',
                                            'order_limit' => 'Order Limit',
                                            'no_of_piece_qty_in_carton' => 'Qty per Carton',
                                        ];
                                    @endphp

                                    @foreach ($columns as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="columns[]"
                                                value="{{ $value }}" checked>
                                            <label class="form-check-label">{{ $label }}</label>
                                        </div>
                                    @endforeach

                                    @foreach ($productList as $product)
                                        @if (request()->has('productsToExport') && in_array($product->id, request()->get('productsToExport')))
                                            <input type="hidden" name="productsToExport[]" value="{{ $product->id }}">
                                        @endif
                                    @endforeach
                                </div>

                                <div class="modal-footer">
                                    <!-- In modal -->
                                    <button type="button" class="btn btn-primary"
                                        onclick="document.getElementById('productListForm').submit();">
                                        Export Selected Products
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Export Columns Selection Modal --}}
                </form>
            </div>
        </div>
        {{-- modal --}}


        <div class="modal fade" id="variantImportModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('product.productVariantsImport') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Product Variants</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <label class="form-label">Upload Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Modal -->
        <form action="{{ route('admin.store.pos.customer') }}" method="post">
            @csrf
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create Customer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12" style="border-right:1px solid #dfdada">
                                    <div class="row mb-2">
                                        <div class="col-sm-4">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Name
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputname" class="form-control" name="name"
                                                    placeholder="Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Phone
                                                <stong class="text-danger">*</stong>
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputname" class="form-control" name="phone"
                                                    placeholder="Phone" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="inputname" class="col-sm-12 col-form-label pr-0">Email
                                            </label>
                                            <div class="col-sm-12">
                                                <input type="text" id="inputname" class="form-control" name="email"
                                                    placeholder="email">
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <label for="supplier_address" class="col-sm-12 col-form-label pr-0">
                                                Address
                                            </label>
                                            <div class="col-sm-12">
                                                <textarea name="supplier_address" class="form-control" id="supplier_address" cols="10" rows="3"
                                                    placeholder="Address"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end p-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>





        {{-- Barcode Modal --}}
        <div class="modal" tabindex="-1" role="dialog" id="barcode">
            <div class="modal-dialog" role="document">
                <form action="{{ route('product.barcode.generate') }}" method="get">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Barcode Generate</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <input type="hidden" name="product_id" id="product_id" required>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-4 col-form-label">Barcode Quantity</label>
                                <div class="col-sm-8">
                                    <input type="number" name="barcode_qty" class="form-control" id="inputEmail3"
                                        placeholder="Barcode Quantity">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Print</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- Barcode Modal --}}


        <!-- Modal Structure -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Excel File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form inside the modal -->
                        <form action="{{ route('product.productsImport') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload Excel File</label>
                                <input type="file" name="file" id="file" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Import</button>
                            <a href="{{ route('admin.products-sample-file') }}" download>Download Sample</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!--end page wrapper -->
@endsection
@section('css_plugins')
    <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    {{-- crop --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
    {{-- crop --}}

    {{-- select2 --}}
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
    {{-- select2 --}}
@endsection
@section('js_plugins')
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/input-tags/js/tagsinput.js"></script>
    {{-- crop --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    {{-- crop --}}

    {{-- select 2 --}}
    <script
        src="{{ asset('assets/adminPanel/plugins') }}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js">
    </script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/input-tags/js/tagsinput.js"></script>
    <script src="{{ asset('assets/adminPanel') }}/plugins/select2/js/select2-custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    {{-- select 2 --}}
@endsection
@section('js')
    <script>
        function submitProductListForm() {
            console.log("submitProductListForm");
            $("#productListForm").submit();
        }

        const imageInput = document.querySelector('.image-input');
        const previewContainer = document.getElementById('image-preview');
        let selectedFiles = []; // Array to keep track of selected files

        imageInput.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            selectedFiles = selectedFiles.concat(files); // Append new files to the array
            updatePreview(); // Call to update the preview
        });

        function updatePreview() {
            previewContainer.innerHTML = ''; // Clear existing previews

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Create a wrapper for the image and remove button
                    const imageWrapper = document.createElement('div');
                    imageWrapper.classList.add('image-wrapper', 'position-relative', 'm-2');

                    // Create the image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail');
                    img.style.width = '150px'; // Set image preview size
                    img.style.height = '200px';

                    // Create the remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-image');
                    removeBtn.innerText = ' x ';

                    // Remove image from preview and deselect from input
                    removeBtn.addEventListener('click', function() {
                        selectedFiles.splice(index, 1); // Remove the file from the array
                        updatePreview(); // Update the preview
                        imageInput.files = createFileList(selectedFiles); // Update the input file list
                    });

                    // Append image and button to the wrapper
                    imageWrapper.appendChild(img);
                    imageWrapper.appendChild(removeBtn);

                    // Append the wrapper to the preview container
                    previewContainer.appendChild(imageWrapper);
                };

                reader.readAsDataURL(file);
            });
        }

        // Helper function to create a new DataTransfer object and update the file input
        function createFileList(files) {
            const dataTransfer = new DataTransfer(); // Use DataTransfer to create a new file list
            files.forEach(file => {
                dataTransfer.items.add(file);
            });
            return dataTransfer.files; // Return the new file list
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#example').DataTable({});
        });
    </script>
    <script>
        function submitVariantExport() {
            let selected = [];

            $('input[name="productsToExport[]"]:checked').each(function() {
                selected.push($(this).val());
            });

            $('#variant_product_ids').val(selected.join(','));
            $('#variantExportForm').submit();
        }

        $('#myInput').tagsinput();

        function editProductInfo(product_id) {
            var url_link = "{{ route('product.edit.info') }}"
            $.ajax({
                url: url_link,
                type: "get",
                data: {
                    product_id: product_id,
                },
                success: function(response) {
                    $('#editProduct').html(response);
                    $('#myInput').tagsinput('refresh');
                    $('.js-example-basic-multiple').select2();
                    $('#inputProductDescription').summernote({
                        height: 200
                    });
                    $('#inputAdditionalInformation').summernote({
                        height: 200
                    });
                },
                error: function(xhr) {
                    //Do Something to handle error
                }
            });

            $('#customer_edit').modal('show');
            // $("#taglist").tagsinput('items')

            $('#myInput').tagsinput();





        }

        function exportProductData() {
            var url_link = "{{ route('admin.product.export') }}"
            $.ajax({
                url: url_link,
                type: "get",
                success: function(response) {

                },
                error: function(xhr) {
                    //Do Something to handle error
                }
            });
        }



        // function tagreset(){
        //     $('#myInput').tagsinput('refresh');
        // }

        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['copy', 'excel', 'pdf', 'print']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>

    <script>
        var bs_modal = $('#modal');
        var image = document.getElementById('image');
        var cropper, reader, file;


        $("body").on("change", ".image", function(e) {
            var files = e.target.files;
            var done = function(url) {
                image.src = url;

                bs_modal.modal('show');
            };

            if (files && files.length > 0) {
                file = files[0];
                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function(e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });


        bs_modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                aspectRatio: 0,
                viewMode: 0,
                preview: '.preview'
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        $("#crop").click(function() {
            canvas = cropper.getCroppedCanvas({
                width: 0,
                height: 0,
            });

            canvas.toBlob(function(blob) {
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    var base64data = reader.result;

                    let inputvaluocation = $('#selectimgdiv').val() + 'input';
                    let viewlocation = $('#selectimgdiv').val() + 'view';

                    $('.' + inputvaluocation).val(base64data)
                    $('.' + viewlocation).html(
                        `  <img class="imgaddborder" src="${base64data}" height="100%" width="100%" alt="">`
                    );

                    $(".modalimage").modal('hide');


                };
            });
        });


        function selectImage(data) {
            $('#selectimgdiv').val(data);
            $('.image').click();
        }

        function removeImage(id) {
            $('#' + id).html(`<div class="remocespen" onclick="removeImage(${id})" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle imgsvg removebtn"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg></div>
                                                                       <div onclick="selectImage(${id})">
                                                                       <input type="hidden" name="product_img[]" class="${id}input">
                                                                           <div class="imgaddcard d-flex justify-content-center align-items-center ${id}view " >
                                                                               <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#171e243d" class="feather feather-camera imgsvg"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                                                           </div>
                                                                        </div>`);
        }

        function deleteItem(id) {
            var url = "{{ route('product.image.delete') }}";
            $.ajax({
                url: url,
                type: "get",
                data: {
                    id: id,
                },
                success: function(response) {
                    if (response == 'success') {
                        $('#' + id).remove()
                    }
                },
                error: function(xhr) {
                    //Do Something to handle error
                }
            });
        }

        function addNewImage() {
            var uniqnumber = new Date().valueOf();
            $('#productImglist').append(`
                                                                      <div class="col-sm-3 mb-2" style="position:relative" id="${uniqnumber}" >
                                                                       <span class="imgeditbtn" onclick="deletenewItem(${uniqnumber})" " ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash text-primary"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span> &nbsp;
                                                                       <div onclick="selectImage(${uniqnumber})">
                                                                       <input type="hidden" name="new_product_img[]" class="${uniqnumber}input">
                                                                           <div class="imgaddcard d-flex justify-content-center align-items-center ${uniqnumber}view " >
                                                                               <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#171e243d" class="feather feather-camera imgsvg"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                                                           </div>
                                                                        </div>
                                                                        </div>
                                                                    `)
        }

        function discountType(data) {
            if ($(data).val() == 0) {
                $('#discount').html(
                    `<label for="inputStarPoints" class="form-label">Discount Amount</label><input type="number" name="discount" class="form-control" placeholder="Amount">`
                )
            }
            if ($(data).val() == 1) {
                $('#discount').html(
                    `  <label for="inputStarPoints" class="form-label">Discount (%)</label>
                                                                                            <input type="number" name="discount" class="form-control" placeholder="Percentage (%)" required>`
                )
            }
        }

        function deletenewItem(id) {
            $('#' + id).remove()
        }

        function barcodePrint(product_id) {

            $('#product_id').val(product_id)
            $('#barcode').modal('show');

        }

        function addnewcolor() {
            // alert('sdfs')
            const color =
                `<span><input type="color" name="product_color[]" class="form-control form-control-color" id="exampleColorInput" value="#563d7c" title="Choose your color"></span>`;
            $('#color').append(color)
        }

        $('.toggle-stock-status').on('click', function() {
            var td = $(this);
            var productId = td.data('product-id');

            $.ajax({
                url: '{{ route('product.toggleStockStatus') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function(response) {
                    if (response.stock_status == 1) {
                        td.html(
                            '<span class="badge bg-primary" style="cursor: pointer;">In Stock</span>'
                        );
                    } else {
                        td.html(
                            '<span class="badge bg-warning" style="cursor: pointer;">Out of Stock</span>'
                        );
                    }
                },
                error: function() {
                    alert('Failed to toggle stock status.');
                }
            });
        });
    </script>
@endsection
