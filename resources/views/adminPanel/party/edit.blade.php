@extends('adminPanel.layout.layout')

@section('title', 'Edit Party')

@section('main_content')
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.parties.update', $party->id) }}" method="POST">
                    @csrf
                    @include('adminPanel.party._form', ['party' => $party])
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection