@extends('adminPanel.layout.layout')
@section('css')
<style>

</style>
@endsection

@section('main_content')
<div class="page-content">
    <input type="hidden" id="selectimgdiv">
    <div class="card">
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{route('page.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="page_id" class="form-label">Select Page<strong
                                        class="text-danger">*</strong></label>
                                <select class="form-control" id="page_id" name="page_id" data-placeholder="Select Page" required>                                        
                                    @foreach($pages as $page)
                                        <option value="{{ $page['id'] }}"> {{ $page['name'] }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="inputName" class="form-label">Page Name<strong
                                        class="text-danger">*</strong> </label>
                                <input type="text" class="form-control" name="name"
                                    id="inputName"
                                    placeholder="Enter Page Name" required>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="inputSlug" class="form-label">Page Slug<strong
                                        class="text-danger">*</strong> </label>
                                <input type="text" class="form-control" name="slug"
                                    id="inputSlug"
                                    placeholder="Enter Page Slug" required>
                                <span id="slugValidateMessage"></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="inputContent" class="form-label">Page Content</label>
                                <textarea class="form-control inputContentClass" name="page_content" id="inputContent" required></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="mt-4"></div>
                    @include('adminPanel.partials.seo_form_fields._create')

                    <div class="col-12">
                        <br />
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Save Page</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css_plugins')
{{-- select2--}}
<link rel="stylesheet"
    href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet"
    href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css" />
{{-- select2--}}
{{-- crop--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
{{-- crop--}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('js_plugins')
{{--select 2--}}
<script src="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/input-tags/js/tagsinput.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/select2/js/select2-custom.js"></script>
{{--select 2--}}
{{-- crop--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
{{-- crop--}}
@endsection
@section('js')

<script>
    $(document).ready(function() {

        $('#page_id').select2();

        $('.js-example-basic-multiple').select2();


        $('#inputBlogDescription').summernote({
            height: 300
        });
        
        $('#inputContent').summernote({
            height: 200
        });

    });


    $('#inputSlug').on('keyup', function() {

        var inputSlug = $(this).val();
        var url_link = "{{route('page.slug.validate')}}";
        $.ajax({
            url: url_link,
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                slug: inputSlug,
            },
            success: function(response) {

                const messageElement = $('#slugValidateMessage');
                if (response.success) {
                    $('#inputSlug').val(response.slug);
                    messageElement.removeClass('text-danger').addClass('text-success').text(response.success);
                } else if (response.error) {
                    messageElement.removeClass('text-success').addClass('text-danger').text(response.error);
                } else {
                    messageElement.text('');
                }

            },
            error: function(xhr) {
                //Error
            }
        });
    });


    $(document).on('click', '.dropdown-toggle', function(ev) {
        $(this).parent().find('.dropdown-menu').show();
    });

    $(document).on('click', '.note-editable.card-block', function() {
        $('.note-dropdown-menu.dropdown-menu').hide();
    });
</script>
@endsection