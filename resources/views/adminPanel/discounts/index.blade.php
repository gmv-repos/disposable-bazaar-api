@extends('adminPanel.layout.layout')

@section('title', 'Discounts List')


@section('main_content')

    <div class="page-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Discounts List</h5>
                <a href="{{ route('discounts.create') }}" class="btn btn-primary btn-sm">Add New Discount</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Discount (%)</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->id }}</td>
                                    <td>{{ $discount->name }}</td>
                                    
                                    <td>{{ $discount->discount_percentage }}</td>
                                    <td>{{ $discount->start_time }}</td>
                                    <td>{{ $discount->end_time }}</td>
                                    <td>{{ $discount->is_active ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <div class="dropdown d-flex justify-content-center">
                                            <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">

                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('discounts.show', $discount->id) }}">
                                                        <i class="lni lni-eye"></i>
                                                        View
                                                    </a>
                                                </li>
                                                <!-- Edit Action -->
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('discounts.edit', $discount->id) }}">
                                                        <i class="lni lni-pencil"></i>
                                                        Edit
                                                    </a>
                                                </li>

                                                <!-- Delete Action -->
                                                <li>

                                                    <form action="{{ route('discounts.destroy', $discount->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="lni lni-trash"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('discounts.duplicate', $discount->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="lni lni-clipboard"></i>
                                                            Duplicate
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
