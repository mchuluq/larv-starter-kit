@extends('layouts.app')

@section('title','user')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            @include('panels.user')
        </div>
        <div class="col-md-6">
            @include('panels.login')
        </div>
    </div>
</div>
@endsection
