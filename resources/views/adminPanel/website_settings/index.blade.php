@extends('adminPanel.layout.layout')

@section('title', 'Settings List')


@section('main_content')

<div class="page-content">
    <div class="card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($websiteSettings as $setting)
                        <tr>
                            <td>{{ $setting->key }}</td>
                            <td>{{ $setting->value  }}</td>
                            <td>{{ $setting->description }}</td>
                            <td>
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="btn btn-primary dropdown-toggle dr-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">

                                        <li>
                                            <a class="dropdown-item" href="">
                                                <i class="lni lni-eye"></i>
                                                View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="">
                                                <i class="lni lni-pencil"></i>
                                                Edit
                                            </a>
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