<script src="{{asset('assets/adminPanel')}}/js/bootstrap.bundle.min.js"></script>
<!--plugins-->
<script src="{{asset('assets/adminPanel')}}/js/jquery.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/simplebar/js/simplebar.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/chartjs/js/Chart.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/plugins/chartjs/js/Chart.extension.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/index.js"></script>

<link rel="stylesheet" href="https://unpkg.com/toastify-js@1.11.2/dist/toastify.min.css">


<!--app JS-->
{{--select 2--}}
{{--<script src="{{asset('assets/adminPanel/plugins')}}/cdn.jsdelivr.net/npm/select2%404.1.0-rc.0/dist/js/select2.min.js"></script>--}}
{{--<script src="{{asset('assets/adminPanel')}}/plugins/select2/js/select2-custom.js"></script>--}}
{{--select 2--}}

<script src="
https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js
"></script>
<link href="
https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css
" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/adminPanel')}}/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

<script>
    @if($message = Session::get('success'))
    let msg = $('#tosterMessage').val();
    Toastify({
        text: msg,
        duration: 3000, // Duration in milliseconds
        newWindow: true,
        close: true,
        gravity: "top", // Position of the toast message
        position: "right", // Position of the toast message
        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)", // Background color of the toast message
    }).showToast();
    @endif

    @if(Session::get('error'))
    let msg = "{{ Session::get('error') }}";
    Toastify({
        text: msg,
        duration: 3000, // Duration in milliseconds
        newWindow: true,
        close: true,
        gravity: "top", // Position of the toast message
        position: "right", // Position of the toast message
        backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)", // Background color of the toast message
    }).showToast();
    @endif
</script>
<script src="{{asset('assets/adminPanel')}}/js/app.js"></script>
@yield('js_plugins')

@yield('js')