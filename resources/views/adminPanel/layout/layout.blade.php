<!doctype html>
<html lang="en">


<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 07 Mar 2023 08:45:44 GMT -->
@include('adminPanel.partials._head')

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @include('adminPanel.partials._sidebar')
        <!--end sidebar wrapper -->
        <!--start header -->
        @include('adminPanel.partials._header')

        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            @yield('main_content')
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <div class="modal fade" id="showDetailModelOneParamerter">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title modalTitle" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="showDetailModelWithoutParamerter">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title modalTitle" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        @include('adminPanel.partials._footer')

    </div>

    @include('adminPanel.partials._scripts')
</body>
<script>
    function printView(divId, style = '', mode = '1') {
        const printContents = document.getElementById(divId).innerHTML;
        const originalContents = document.body.innerHTML;
        const printWindow = window.open('', '', 'height=600,width=800');

        // Get style tags from head
        const styles = [...document.querySelectorAll('style, link[rel="stylesheet"]')]
            .map(style => style.outerHTML)
            .join('\n');

        printWindow.document.write(`
            <html>
                <head>
                    <title>Print</title>
                    ${styles}
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        table, th, td { border: 1px solid #ccc; }
                        th, td { padding: 8px; text-align: center; }
                        thead { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    ${printContents}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
</script>

<script>
    function showDetailModelOneParamerter(url, id, modalName) {
        $.ajax({
            url: '<?php echo url('/'); ?>/' + url + '',
            type: "GET",
            data: {
                id: id
            },
            success: function (data) {
                jQuery('#showDetailModelOneParamerter').modal('show', {
                    backdrop: 'false'
                });
                jQuery('#showDetailModelOneParamerter .modalTitle').html(modalName);
                jQuery('#showDetailModelOneParamerter .modal-body').html(
                    '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>'
                );
                setTimeout(function () {
                    jQuery('#showDetailModelOneParamerter .modal-body').html(data);
                }, 1000);
            }
        });
    }

    function showDetailModelWithoutParamerter(url, modalName) {
        $.ajax({
            url: '<?php echo url('/'); ?>/' + url + '',
            type: "GET",
            data: {},
            success: function (data) {
                jQuery('#showDetailModelWithoutParamerter').modal('show', {
                    backdrop: 'false'
                });
                jQuery('#showDetailModelWithoutParamerter .modalTitle').html(modalName);
                jQuery('#showDetailModelWithoutParamerter .modal-body').html(
                    '<div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="loader"></div></div></div>'
                );
                setTimeout(function () {
                    jQuery('#showDetailModelWithoutParamerter .modal-body').html(data);
                }, 1000);
            }
        });
    }


    function markNotificationAsRead(notificationId, element) {
        $.ajax({
            url: '{{ route('notification.mark.as.read') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                notification_id: notificationId
            },
            success: function (response) {
                //
            }
        });
    }


    function successToast(text) {
        Toastify({
            text: text,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
        }).showToast();
    }

    function errorToast(text) {
        Toastify({
            text: text,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
        }).showToast();
    }

    function warningToast(text) {
        Toastify({
            text: text,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #f7b42c, #fc575e)",
        }).showToast();
    }
</script>

<script>
    $(document).ready(function () {
        $('.dtReport').DataTable({
            dom: "<'row align-items-center mb-3'<'col-md-4'l><'col-md-4 text-center'f><'col-md-4 text-end'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 text-end'p>>",
            buttons: ['copy', 'excel', 'pdf', 'print']
        });
    });
</script>

</html>