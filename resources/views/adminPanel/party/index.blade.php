@extends('adminPanel.layout.layout')

@section('title', 'Parties List')

@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Parties List</h4>
                <a href="{{ route('admin.parties.create') }}" class="btn btn-primary btn-sm">
                    Add New
                </a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>S#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone 1</th>
                                <th>Phone 2</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parties as $party)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $party->name }}</td>
                                    <td>{{ $party->email }}</td>
                                    <td>{{ $party->phone_one }}</td>
                                    <td>{{ $party->phone_two }}</td>
                                    <td>{{ $party->address }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('admin.parties.edit', $party->id) }}" class="btn btn-sm btn-warning mx-1">
                                            <i class="lni lni-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.parties.destroy', $party->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this item?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger mx-1">
                                                <i class="lni lni-trash"></i>
                                            </button>
                                        </form>
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

@section('js')
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>
@endsection