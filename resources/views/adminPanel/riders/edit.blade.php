@extends('adminPanel.layout.layout')

@section('main_content')
<div class="page-content">

    <div class="card">
        <div class="card-body p-4">
            <div class="form-body mt-4">
                <form action="{{ route('riders.update', $rider->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                placeholder="Enter rider name"
                                value="{{ old('name', $rider->name) }}"
                                required>
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input
                                type="number"
                                name="phone"
                                id="phone"
                                class="form-control"
                                placeholder="Enter phone number"
                                value="{{ old('phone', $rider->phone) }}"
                                required>
                            @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email', $rider->email) }}">
                            @error('email')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input
                                type="text"
                                name="address"
                                id="address"
                                class="form-control"
                                value="{{ old('address', $rider->address) }}"
                                required>
                            @error('address')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 ms-2">
                            <b class="fw-bold">Status</b>
                            <div class="row pt-2">
                                <div class="col-auto">
                                    <input
                                        type="radio"
                                        name="status"
                                        id="active"
                                        class="form-check-input"
                                        value="active"
                                        {{ old('status', $rider->status) === 'active' ? 'checked' : '' }}
                                        required>
                                    <label for="active" class="form-check-label">Active</label>
                                </div>
                                <div class="col-auto">
                                    <input
                                        type="radio"
                                        name="status"
                                        id="inactive"
                                        class="form-check-input"
                                        value="inactive"
                                        {{ old('status', $rider->status) === 'inactive' ? 'checked' : '' }}
                                        required>
                                    <label for="inactive" class="form-check-label">Inactive</label>
                                </div>
                            </div>
                            @error('status')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary">Update Rider</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
