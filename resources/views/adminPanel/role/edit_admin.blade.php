<div class="row">
    <div class="col-sm-12" style="border-right:1px solid #dfdada">
        <div class="mb-2 row">
            <div class="col-sm-12">
                <input type="hidden" name="admin_id" value="{{$adminInfo->id}}">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Name
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <input type="text" id="inputname" class="form-control"
                            name="name"
                            placeholder="Name" required value="{{$adminInfo->name}}">
                </div>
            </div>
            <div class="col-sm-12">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Email
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <input type="text" id="inputname" class="form-control"
                            name="email"
                            placeholder="Name" value="{{$adminInfo->email}}" required>
                </div>
            </div>
            <div class="col-sm-12 mt-2">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Role
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <select name="role_id" class="form-control" id="" required>
                        <option value="">SELECT ROLE</option>
                        @foreach($role as $roledata)
                            <option value="{{$roledata->id}}" @if($adminInfo->admin_type == $roledata->id) selected @endif>{{$roledata->name}}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-sm-12 mt-2">
                <label for="inputname" class="col-sm-12  pr-0 col-form-label">Password
                    <stong class="text-danger">*</stong>
                </label>
                <div class="col-sm-12">
                    <input type="password" id="inputname" class="form-control"
                            name="password"
                            placeholder="Password" required>
                </div>
            </div>


        </div>
    </div>
</div>