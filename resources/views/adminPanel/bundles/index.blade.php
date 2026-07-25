@extends('adminPanel.layout.layout')

@section('title', 'Bundles List')

@section('main_content')

<div class="page-content">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Bundles List</h4>
            <a href="{{ route('bundles.create') }}" class="btn btn-primary btn-sm">
                Add New
            </a>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Reference</th>
                            <th>Name</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Payable Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bundles as $bundle)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $bundle->reference_code }}</td>
                            <td>{{ $bundle->name }}</td>
                            <td>{{ $bundle->total_amount }}</td>
                            <td>{{ $bundle->discount_amount }}</td>
                            <td>{{ $bundle->payable_amount }}</td>
                            <td class="d-flex">
                                
                                <a href="{{ route('bundles.show', $bundle->id) }}" class="btn btn-sm btn-info mx-1">
                                    <i class="lni lni-eye"></i>
                                </a>

                                <a href="{{ route('bundles.edit', $bundle->id) }}" class="btn btn-sm btn-warning mx-1">
                                    <i class="lni lni-pencil"></i>
                                </a>
                                <form action="{{ route('bundles.destroy', $bundle->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mx-1">
                                        <i class="lni lni-trash"></i>
                                    </button>
                                </form>
                            </td>
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
    $(document).ready(function() {
        $('#example').DataTable();
    });


</script>
@endsection