@extends('adminPanel.layout.layout')

@section('title', 'Lid Options List')


@section('main_content')

<div class="page-content">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Lid Options List</h4>
            <button type="button" class="btn btn-primary btn-sm" onclick="addNewLidOption()">
                Add New
            </button>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S No</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Image Alt</th>
                            <th>Image Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lidOptions as $key => $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->name  }}</td>
                            <td>
                                <img src="{{ asset($value->image) }}" width="100" alt="{{ $value->img_alt }}">
                            </td>
                            <td>{{ $value->img_alt  }}</td>
                            <td>{{ $value->img_name  }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                      
                                        <!-- Edit Action -->
                                        <li>
                                            <button class="dropdown-item" href="#" onclick="editLidOption('{{ $value->id }}')">
                                                <i class="lni lni-pencil"></i>
                                                Edit
                                            </button>
                                        </li>

                                        <!-- Delete Action -->
                                        <li>

                                            <form action="{{ route('product.lids.destroy', $value->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Lid Option?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="lni lni-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Lid Option Add / Edit Modal -->
<div class="modal fade" id="LidOptionsModal" tabindex="-1" aria-labelledby="LidOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header posTopbar">
                <h5 class="modal-title" id="LidOptionsModalLabel">-</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="LidOptionsModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function addNewLidOption() {

        $.ajax({
            url: "{{route('product.lids.create')}}",
            method: "GET",
            success: function(response) {
                $('#LidOptionsModalLabel').text('Add New Lid Option');
                $('#LidOptionsModalBody').html(response.html);
                $('#LidOptionsModal').modal('show');
            },
            error: function(xhr) {
                console.log("AJAX Error", xhr);
            }
        });
    }


    function storeNewLidOption(form) {
        event.preventDefault();
        var formData = new FormData(form);
        $.ajax({
            url: $(form).attr('action'),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#LidOptionsModalBody').html('');
                $('#LidOptionsModal').modal('hide');
                Toastify({
                    text: response.message,
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #28a745, #34bfa3)"
                }).showToast();

                window.location.reload();
            },
            error: function(xhr) {

                console.log("AJAX Error", xhr);

                let errors = xhr.responseJSON.errors;
                let errorMessage = errors ? Object.values(errors)[0][0] : "An error occurred. Please try again.";

                Toastify({
                    text: errorMessage,
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                }).showToast();
            }
        });
    }

    function editLidOption(id) {
        $.ajax({
            url: `{{ route('product.lids.edit', ':id') }}`.replace(':id', id),
            method: "GET",
            success: function(response) {
                $('#LidOptionsModalLabel').text('Edit Lid Option');
                $('#LidOptionsModalBody').html(response.html);
                $('#LidOptionsModal').modal('show');
            },
            error: function(xhr) {
                console.log("AJAX Error", xhr);
            }
        });
    }

    function updateLidOption(form) {
        event.preventDefault();
        var formData = new FormData(form);
        $.ajax({
            url: $(form).attr('action'),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (!response.success) {
                    return false
                }

                $('#LidOptionsModalBody').html('');
                $('#LidOptionsModal').modal('hide');
                Toastify({
                    text: response.message,
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #28a745, #34bfa3)"
                }).showToast();

                window.location.reload();
            },
            error: function(xhr) {

                console.log("AJAX Error", xhr);

                let errors = xhr.responseJSON.errors;
                let errorMessage = errors ? Object.values(errors)[0][0] : "An error occurred. Please try again.";

                Toastify({
                    text: errorMessage,
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #FF5F6D, #FFC371)",
                }).showToast();
            }
        });
    }
</script>

@endsection