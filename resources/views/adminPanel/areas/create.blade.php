@extends('adminPanel.layout.layout')
@section('css')
<style>

</style>
@endsection

@section('main_content')
<div class="page-content">
    <input type="hidden" id="selectimgdiv">
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <button class="btn btn-primary btn-sm px-3" onclick="openAreaImportModal()">Import Excel File</button>
        </div>
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{route('admin.store.areas')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="border border-3 p-4 rounded">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label for="inputAreaName" class="form-label">Area Name<strong
                                                    class="text-danger">*</strong> </label>
                                            <input type="text" class="form-control" name="area_name"
                                                id="inputAreaName"
                                                placeholder="Enter Area Name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label for="inputCityName" class="form-label">City<strong
                                                    class="text-danger">*</strong> </label>
                                            <input type="text" class="form-control" name="city_name"
                                                id="inputCityName"
                                                placeholder="Enter City Name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label for="inputShippingCharges" class="form-label">Shipping Charges<strong
                                                    class="text-danger">*</strong> </label>
                                            <input type="number" class="form-control" name="shipping_rate"
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
                                <button type="submit" class="btn btn-primary">Save Area</button>
                            </div>
                        </div>
                    </div><!--end row-->
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="importAreaModal" tabindex="-1" aria-labelledby="importAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importAreaModalLabel">Select Excel File</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.areas.import.excel') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="file" name="excelfile" class="form-control form-control-sm">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Import</button>
                </div>
            </form>

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
    function openAreaImportModal() {
        $('#importAreaModal').modal('show');
    }
</script>
@endsection