@extends('layouts.app')

@section('title','User')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 mb-3">
            @include('panels.user')
        </div>
        <div class="col-md-6 mb-3">
            @include('panels.login')
        </div>
        <div class="col-md-6 mb-3">
            @include('panels.otp')
        </div>
        <div class="col-md-6 mb-3">
            @include('panels.webauthn')
        </div>
    </div>
</div>
@endsection
