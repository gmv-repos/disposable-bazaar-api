@extends('adminPanel.layout.layout')

@section('title', 'Riders List')


@section('main_content')

<div class="page-content">
    <div class="card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riders as $rider)
                        <tr>
                            <td>{{ $rider->id }}</td>
                            <td>{{ $rider->name  }}</td>
                            <td>{{ $rider->phone }}</td>
                            <td>{{ $rider->address }}</td>
                            <td>{{ $rider->status }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">

                                        <li>
                                            <a class="dropdown-item" href="{{ route('riders.show', $rider->id) }}">
                                                <i class="lni lni-eye"></i>
                                                View
                                            </a>
                                        </li>
                                        <!-- Edit Action -->
                                        <li>
                                            <a class="dropdown-item" href="{{ route('riders.edit', $rider->id) }}">
                                                <i class="lni lni-pencil"></i>
                                                Edit
                                            </a>
                                        </li>

                                        <!-- Delete Action -->
                                        <li>

                                            <form action="{{ route('riders.destroy', $rider->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this rider?');">
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
@endsection