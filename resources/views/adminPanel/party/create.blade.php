@extends('adminPanel.layout.layout')

@section('title', 'Create Party')

@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.parties.store') }}" method="POST">
                    @csrf
                    @include('adminPanel.party._form')
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection