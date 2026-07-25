<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $party->name ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $party->email ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone_one">Phone One</label>
        <input type="text" name="phone_one" class="form-control" value="{{ old('phone_one', $party->phone_one ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="phone_two">Phone Two</label>
        <input type="text" name="phone_two" class="form-control" value="{{ old('phone_two', $party->phone_two ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label for="address">Address</label>
        <textarea name="address" class="form-control" rows="3">{{ old('address', $party->address ?? '') }}</textarea>
    </div>
</div>
