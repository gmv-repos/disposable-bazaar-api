@extends('adminPanel.layout.layout')
@section('css')
    <style>
        
    </style>
@endsection

@section('main_content')

    <div class="page-content">
        <div class="card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Area Name</th>
                                <th>City Name</th>
                                <th>Shiiping Charges</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                                <tr>
                                    <td>{{ $area->id }}</td>
                                    <td>{{ $area->area_name }}</td>
                                    <td>{{ $area->city_name}}</td>
                                    <td>{{round($area->shipping_rate)}}</td>
                                    @if($area->status == 1)
                                       <td><span class="badge bg-success">Active</span></td>
                                    @else
                                       <td><span class="badge bg-danger">Inactive</span></td>
                                   @endif
                                    <td>
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Settings
                                            </button>
                                            <ul class="dropdown-menu" style="">
                                                @if($area->status == 1)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('areas.edit', $area->id) }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="feather feather-edit text-primary">
                                                                <path
                                                                    d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                                <path
                                                                    d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                            </svg>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li class="align-items-center" onclick="return confirm('Are you sure you want to delete this item?');"><a class="dropdown-item" href="{{route('admin.delete.areas',['id'=>$area->id])}}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash text-primary"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                        Delete</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                        
                                        <!-- Add more actions if needed -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
            $('#example').DataTable({});
        });
    </script>
@endsection