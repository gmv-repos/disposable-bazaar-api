@extends('adminPanel.layout.layout')

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
@section('main_content')
    <!--start page wrapper -->
    <div class="page-content">
        <!--breadcrumb-->

        <!--end breadcrumb-->
        <div class="card">
            <input type="hidden" id="selectimgdiv">
            <div class="card-body">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="ms-auto">
                        <div class="btn-group">
                            <div class="d-flex mt-3 gap-3">
                                <a href="{{ route('admin.product.category.sort.orders') }}" class="btn btn-primary btn-sm">
                                    Sorting
                                </a>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">
                                    <i class="lni lni-circle-plus"></i> Add Category
                                </button>
                                {{-- <a href="#" class="btn btn-primary"><i class="lni lni-circle-plus"></i> Add Category</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="example" class="table-striped table-bordered table" style="width:100%">

                        <thead>
                            <tr class="t-trcolor">
                                <th>S.NO.</th>
                                <th>Parent Category</th>
                                <th>Category Name</th>
                                <th>Image</th>
                                {{-- <th>Description</th> --}}
                                <th>Is Popular</th>
                                <th>Status</th>
                                <th>Create Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($category as $key => $categorylist)
                            <tr>
                                <td>{{ $categorylist->serial_no }}</td>
                                <td>{{ $categorylist->parentCategory->name ?? '-' }}</td>
                                <td>{{ $categorylist->name }}</td>
                                <td class="d-flex justify-content-center">
                                    <img class="productImglistst"
                                        src="{{ asset($categorylist->image ?: 'assets/adminPanel/images/dummy.png') }}"
                                        alt="{{ $categorylist->name }}">
                                </td>
                                {{-- <td>{{ $categorylist->note }}</td> --}}

                                @if ($categorylist->is_popular == 1)
                                    <td><span class="badge bg-success">Popular</span></td>
                                @else
                                    <td><span class="badge bg-danger">Not Popular</span></td>
                                @endif

                                @if ($categorylist->status == 1)
                                    <td><span class="badge bg-success">Active</span></td>
                                @else
                                    <td><span class="badge bg-danger">Inactive</span></td>
                                @endif
                                <td>{{ date('d-M-y', strtotime($categorylist->created_at)) }}</td>
                                <td>
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu" style="">
                                            <li
                                                onclick="viewCategoryData({{ $categorylist }},'{{ asset($categorylist->image) }}')">
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-eye text-primary">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    View
                                                </a>
                                            </li>
                                            <li
                                                onclick="editCategoryData({{ $categorylist }},'{{ asset($categorylist->image) }}')">
                                                <a class="dropdown-item" href="#">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-edit text-primary">
                                                        <path
                                                            d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                        </path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </li>
                                            @if ($categorylist->status == 1)
                                                <li class="align-items-center"
                                                    onclick="return confirm('Are you sure you want to Inactive this item?');">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.inactive.category', ['id' => $categorylist->id]) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-trash text-primary">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                        </svg>
                                                        Inactive</a>
                                                </li>
                                            @else
                                                <li class="align-items-center"
                                                    onclick="return confirm('Are you sure you want to Active this item?');">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.active.category', ['id' => $categorylist->id]) }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-trash text-primary">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                        </svg>
                                                        Active</a>
                                                </li>
                                            @endif

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach

                            </tr>


                        </tbody>
                        {{-- <tfoot> --}}
                        {{-- <tr> --}}
                        {{-- <th>1</th> --}}
                        {{-- <th>Position</th> --}}
                        {{-- <th>Office</th> --}}
                        {{-- <th>Age</th> --}}
                        {{-- <th>Start date</th> --}}
                        {{-- </tr> --}}
                        {{-- </tfoot> --}}
                    </table>
                </div>
            </div>
        </div>
        {{-- modal --}}
        <!-- Modal -->
        <form action="{{ route('admin.store.category') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-2">
                                <label for="parent_id" class="col-sm-12 col-form-label pr-0">Parent</label>
                                <div class="col-sm-12">
                                    <select id="create_parent_id" class="form-control parentCategory" name="parent_id">
                                        <option value="">Select Parent Category</option>
                                        @foreach ($categoryDD as $cate)
                                            <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="inputname" class="col-sm-12 col-form-label pr-0">Category Name <stong
                                        class="text-danger">*</stong></label>
                                <div class="col-sm-12">
                                    <input type="text" id="inputname" class="form-control" name="name"
                                        placeholder="Category Name">
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                <label for="inputserial_no" class="col-sm-12 col-form-label pr-0">
                                    Serial No
                                </label>
                                <div class="col-sm-12">
                                    <input type="number" id="inputserial_no" class="form-control" name="serial_no"
                                        onkeyup="validateSerialNo(this, 1)">
                                    <span class="serialNoValidateMessage"></span>
                                </div>
                            </div> --}}
                            <div class="row mb-2">
                                <label for="inputSlug" class="col-sm-12 col-form-label pr-0">Category Slug<stong
                                        class="text-danger">*</stong></label>
                                <div class="col-sm-12">
                                    <input type="text" id="inputSlug" onkeyup="validateSlug(this, 1)"
                                        class="form-control" name="slug" placeholder="Category Slug">
                                    <span class="slugValidateMessage"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="description" class="col-sm-12 col-form-label pr-0">Description</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control catDesc" id="description" name="note" id="" cols="30"
                                        rows="3"></textarea>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="col-md-12">
                                    <label for="">
                                        Banner Image
                                    </label>
                                    <input type="file" name="hero_banner_image" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12 mt-2">
                                    <label for="inputProductDescription" class="form-label">Category Icon</label>
                                    <input style="display:none" type="file" name="image" class="image">
                                    <div class="row d-flex justify-content-center" id="productImglist">
                                        <div class="col-sm-4 mb-2" style="position:relative" id="222"
                                            onclick="selectImage('222')">
                                            <span class="mainphototxt text-center">Main Photo</span>
                                            <input type="hidden" name="banner_img" class="222input">
                                            <div
                                                class="imgaddcard d-flex justify-content-center align-items-center 222view">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-camera text-primary imgsvg">
                                                    <path
                                                        d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z">
                                                    </path>
                                                    <circle cx="12" cy="13" r="4"></circle>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade modalimage" id="modal" tabindex="-1" role="dialog"
                                        aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel">Crop image</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">�</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="img-container">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <!--  default image where we will set the src via jquery-->
                                                                <img id="image">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="preview"></div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Cancel
                                                    </button>
                                                    <button type="button" class="btn btn-primary" id="crop">Crop
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="m-lg-1 d-flex my-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_popular" value="1"
                                            id="activecheck" checked>
                                        <label class="form-check-label" for="defaultCheck1">
                                            Is Popular
                                        </label>
                                    </div>

                                    <div class="form-check mx-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="statuscheck1" checked>
                                        <label class="form-check-label" for="statuscheck1">
                                            Is Active
                                        </label>
                                    </div>

                                </div>
                            </div>

                            @include('adminPanel.partials.seo_form_fields._create')

                        </div>
                        <div class="d-flex justify-content-end p-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Edit --}}
        <form action="{{ route('admin.update.category') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal fade" id="category_edit" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Update Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="row mb-2">
                                <label for="ed_parent_id" class="col-sm-12 col-form-label pr-0">Parent</label>
                                <div class="col-sm-12">
                                    <select id="ed_parent_id" class="form-control parentCategory" name="parent_id">
                                        <option value="">Select Parent Category</option>
                                        @foreach ($categoryDD as $cate)
                                            <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <input type="hidden" name="category_id" id="category_id">
                                <label for="inputname" class="col-sm-12 col-form-label pr-0">Category Name <stong
                                        class="text-danger">*</stong></label>
                                <div class="col-sm-12">
                                    <input type="text" id="ed_name" class="form-control" name="name"
                                        placeholder="Category Name" required>
                                </div>
                            </div>

                            {{-- <div class="row mb-2">
                                <label for="ed_serial_no" class="col-sm-12 col-form-label pr-0">
                                    Serial No
                                </label>
                                <div class="col-sm-12">
                                    <input type="number" id="ed_serial_no" class="form-control" name="serial_no"
                                        onkeyup="validateSerialNo(this, 2)">
                                    <span class="serialNoValidateMessage"></span>
                                </div>
                            </div> --}}

                            <div class="row mb-2">
                                <label for="ed_slug" class="col-sm-12 col-form-label pr-0">Category Slug<stong
                                        class="text-danger">*</stong></label>
                                <div class="col-sm-12">
                                    <input type="text" id="ed_slug" onkeyup="validateSlug(this, 2)"
                                        class="form-control" name="slug" placeholder="Category Slug">
                                    <span class="slugValidateMessage"></span>
                                </div>
                            </div>

                            {{-- <div class="mb-2 row"> --}}
                            {{-- <label for="inputPasswordww" class="col-sm-12  pr-0 col-form-label">Category Image</label> --}}
                            {{-- <div class="col-sm-12"> --}}
                            {{-- <input type="file" class="form-control" name="img" id="inputPasswordww" placeholder="Category Name"> --}}
                            {{-- </div> --}}
                            {{-- </div> --}}

                            <div class="row mb-2">
                                <label for="ed_description" class="col-sm-12 col-form-label pr-0">Description</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control catDesc" id="ed_description" name="note" cols="30" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="col-md-12">
                                    <label for="">
                                        Banner Image
                                    </label>
                                    <input type="file" name="hero_banner_image" class="form-control">
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center mb-2 mt-2" style="position: relative">
                                <label for="inputPasswordww" class="col-sm-12 col-form-label pr-0">Category Image</label>
                                <div class="d-flex justify-content-center">
                                    <span onclick="changeBrand()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-edit text-primary">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </span>
                                    <img id="updateimg"
                                        style=" height: 140px;width: 140px;border: 1px solid #e5e2e2;border-radius: 20px;padding: 0px;"
                                        src="" alt="">
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center" id="productImglist">
                                <input style="display: none" id="inp" type="file">
                                <input style="display: none" id="inp2" name="updateImage" type="text">
                            </div>

                            <div class="m-lg-1 d-flex my-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_popular" value="1"
                                        id="activecheck" checked>
                                    <label class="form-check-label" for="defaultCheck1">
                                        Is Popular
                                    </label>
                                </div>
                                <div class="form-check mx-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="statuscheck" checked>
                                    <label class="form-check-label" for="statuscheck">
                                        Is Active
                                    </label>
                                </div>
                            </div>

                            @include('adminPanel.partials.seo_form_fields._edit', ['seoMetadata' => null])

                        </div>
                        <div class="d-flex justify-content-end p-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- modal --}}


        <!-- View Category Modal Start Here -->

        <div class="modal fade" id="category_view" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Category Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Name</th>
                                            <td id="category_name"></td>
                                        </tr>
                                        {{-- <tr>
                                            <th>Serial No</th>
                                            <td id="category_serial_no"></td>
                                        </tr> --}}
                                        <tr>
                                            <th>Description</th>
                                            <td id="category_description"></td>
                                        </tr>
                                        <tr>
                                            <th>Is Popular</th>
                                            <td id="category_is_popular"></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td id="category_status"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12">
                                <img id="category_img" class="w-100" src="" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Category Modal End Here -->



        </div>
        <!--end page wrapper -->
    @endsection
    @section('css_plugins')
        <link href="{{ asset('assets/adminPanel') }}/plugins/datatable/css/dataTables.bootstrap5.min.css"
            rel="stylesheet" />
        {{-- crop --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
        {{-- crop --}}

        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    @endsection
    @section('js_plugins')
        <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('assets/adminPanel') }}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
        {{-- crop --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
        {{-- crop --}}

        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    @endsection
    @section('js')
        <script>
            $('body').on('shown.bs.modal', function(e) {
                var modal = $(e.target); // The modal being shown
                modal.find(".parentCategory").select2({
                    dropdownParent: modal
                });
            });

            function viewCategoryData(data, img) {
                $('#category_view #category_name').text(data.name);
                $('#category_view #category_serial_no').text(data.serial_no);
                $('#category_view #category_description').text(data.note);
                $('#category_view #category_is_popular').text(data.is_popular == 1 ? 'Yes' : 'No');
                $('#category_view #category_status').text(data.status == 1 ? 'Active' : 'InActive');
                $('#category_view #category_img').attr("src", img);
                $('#category_view').modal('show');
            }

            function editCategoryData(data, img) {

                $('#ed_name').val(data.name)
                $('#ed_serial_no').val(data.serial_no)
                $('#ed_slug').val(data.slug)
                $('#ed_description').summernote('code', data.note);
                $('#category_id').val(data.id)
                $('#updateimg').attr("src", img);
                $('#ed_parent_id').val(data.parent_id).change();


                if (data.category_seo_metadata) {
                    $('.editSeoMetadata #inputMetaTitle').val(data.category_seo_metadata.meta_title || "");
                    $('.editSeoMetadata #inputCanonicalUrl').val(data.category_seo_metadata.canonical_url || "");
                    $('.editSeoMetadata #inputFocusKeyword').val(data.category_seo_metadata.focus_keyword || "");
                    $('.editSeoMetadata #inputRedirect301').val(data.category_seo_metadata.redirect_301 || "");
                    $('.editSeoMetadata #inputRedirect302').val(data.category_seo_metadata.redirect_302 || "");
                    $('.editSeoMetadata #inputSchema').val(data.category_seo_metadata.schema || "");
                    $('.editSeoMetadata #inputMetaDescription').html(data.category_seo_metadata.meta_description || "");
                }




                $('#category_edit').modal('show')
            }


            $(document).ready(function() {

                $('.catDesc').summernote({
                    height: 200
                });

                $(document).on('click', '.dropdown-toggle', function(ev) {
                    $(this).parent().find('.dropdown-menu').show();
                });

                $(document).on('click', '.note-editable.card-block', function() {
                    $('.note-dropdown-menu.dropdown-menu').hide();
                });

                // $('#example').DataTable();
                $('#example').DataTable({
                    "dom": 'rtip'
                    // paging: false,
                    // ordering: false,
                    // info: false,
                });
            });
        </script>
        <script>
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
                    width: 500,
                    height: 400,
                });

                canvas.toBlob(function(blob) {
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        var base64data = reader.result;

                        let inputvaluocation = $('#selectimgdiv').val() + 'input';
                        let viewlocation = $('#selectimgdiv').val() + 'view';
                        var uniqnumber = new Date().valueOf();

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


            function readFile() {
                if (!this.files || !this.files[0]) return;
                const FR = new FileReader();
                FR.addEventListener("load", function(evt) {
                    document.querySelector("#updateimg").src = evt.target.result;
                    // document.querySelector("#inp2").val = evt.target.result;
                    $('#inp2').val(evt.target.result);
                });
                FR.readAsDataURL(this.files[0]);
            }

            document.querySelector("#inp").addEventListener("change", readFile);

            function changeBrand() {
                $('#inp').click();
            }
        </script>



        <script>
            $(document).on('click', '.dropdown-toggle', function(ev) {
                $(this).parent().find('.dropdown-menu').show();
            });

            $(document).on('click', '.note-editable.card-block', function() {
                $('.note-dropdown-menu.dropdown-menu').hide();
            });



            var typingTimer;

            function validateSlug(element, option = 1) {
                clearTimeout(typingTimer);
                $('.slugValidateMessage').text('');
                var slug = $(element).val();
                var id = '';
                if (option == 2) {
                    id = $('#category_id').val();
                }
                var url_link = "{{ route('category.slug.validate') }}";
                typingTimer = setTimeout(function() {
                    $.ajax({
                        url: url_link,
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            slug: slug,
                            id: id,
                        },
                        success: function(response) {
                            var messageElement = $(element).siblings('.slugValidateMessage');
                            if (response.success) {
                                $(element).val(response.slug);
                                messageElement.removeClass('text-danger').addClass('text-success').text(
                                    response.success);
                            } else if (response.error) {
                                messageElement.removeClass('text-success').addClass('text-danger').text(
                                    response.error);
                            } else {
                                messageElement.text('');
                            }
                        },
                        error: function(xhr) {}
                    });
                }, 1000);
            }

            function validateSerialNo(element, option = 1) {
                $('.serialNoValidateMessage').text('');
                var serial_no = $(element).val();
                var parentCategory = '';
                if (option == 2) {
                    parentCategory = $('#ed_parent_id').val();
                } else {
                    parentCategory = $("#create_parent_id").val();
                }
                var id = '';
                if (option == 2) {
                    id = $('#category_id').val();
                }
                var url_link = "{{ route('category.serial_no.validate') }}";
                typingTimer = setTimeout(function() {
                    $.ajax({
                        url: url_link,
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            parent_id: parentCategory,
                            serial_no: serial_no,
                            id: id,
                        },
                        success: function(response) {
                            var messageElement = $(element).siblings('.serialNoValidateMessage');
                            if (response.success) {
                                $(element).val(response.serial_no);
                                messageElement.removeClass('text-danger').addClass('text-success').text(
                                    response.success);
                            } else if (response.error) {
                                $(element).val("");
                                messageElement.removeClass('text-success').addClass('text-danger').text(
                                    response.error);
                            } else {
                                messageElement.text('');
                            }
                        },
                        error: function(xhr) {}
                    });
                }, 1000);
            }
        </script>
    @endsection
