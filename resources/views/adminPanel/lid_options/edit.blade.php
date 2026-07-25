<form action="{{ route('product.lids.update', $lidOption->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Name</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                placeholder="Enter Lid Option Name"
                value="{{ $lidOption->name }}">
        </div>
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Image</label>
            <input
                type="file"
                name="image"
                id="image"
                class="form-control"                
                >
        </div>
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">IMG Alt</label>
            <input
                type="text"
                name="img_alt"
                id="img_alt"
                class="form-control"
                value="{{ $lidOption->img_alt }}"
                >
        </div>
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">IMG Name</label>
            <input
                type="text"
                name="img_name"
                id="img_name"
                class="form-control"
                value="{{ $lidOption->img_name }}"
                >
        </div>
        <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-success w-100" onclick="updateLidOption(this.form)">
                Update Lid Option
            </button>
        </div>
    </div>
</form>