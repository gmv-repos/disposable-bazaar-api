<form action="{{ route('product.lids.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Name</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                placeholder="Enter Lid Option Name"
                >
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
                >
        </div>
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">IMG Name</label>
            <input
                type="text"
                name="img_name"
                id="img_name"
                class="form-control"
                >
        </div>
        <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-success w-100" onclick="storeNewLidOption(this.form)">
                Create Lid Option
            </button>
        </div>
    </div>
</form>