@php
    use Carbon\Carbon;
@endphp
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
                    <form action="{{route('admin.update.areas')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $area->id }}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="border border-3 p-4 rounded">

                                <div class="row">
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputAreaName" class="form-label">Area Name<strong
                                                        class="text-danger">*</strong> </label>
                                                <input type="text" class="form-control" name="area_name" value="{{ $area->area_name }}"
                                                       id="inputAreaName"
                                                       placeholder="Enter Area Name" required >
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label for="inputCityName" class="form-label">City<strong
                                                        class="text-danger">*</strong> </label>
                                                <input type="text" class="form-control" name="city_name" value="{{$area->city_name}}" placeholder
                                                       id="inputCityName"
                                                       placeholder="Enter City Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                        <div class="mb-3">
                                                <label for="inputShippingCharges" class="form-label">Shipping Charges<strong
                                                        class="text-danger">*</strong> </label>
                                                <input type="number" class="form-control" name="shipping_rate" value="{{$area->shipping_rate}}"
                                                       id="inputShippingCharges"
                                                       placeholder="Enter Shipping Charges" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <br />
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Update Area</button>
                                </div>
                            </div>
                        </div><!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css_plugins')
    {{--    select2--}}
    <link rel="stylesheet"
          href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/css/select2.min.css"/>
    <link rel="stylesheet"
          href="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2-bootstrap-5-theme%401.3.0/dist/select2-bootstrap-5-theme.min.css"/>
    {{--    select2--}}
    {{--    crop--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css"/>
    {{--    crop--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('js_plugins')
    {{--select 2--}}
        <script src="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{asset('assets/adminPanel')}}/plugins/input-tags/js/tagsinput.js"></script>
        <script src="{{asset('assets/adminPanel')}}/plugins/select2/js/select2-custom.js"></script>
    {{--select 2--}}
    {{--    crop--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
    {{--    crop--}}
@endsection
