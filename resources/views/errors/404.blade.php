@extends('errors.layout')

@section('title', 'Page Not Found')
@section('code', '404')
@section('icon', 'fa-compass')
@section('heading', 'Page not found')
@section('message', "The page you are looking for doesn't exist or has been moved.")

@section('actions')
    <a href="javascript:history.back()" class="btn btn-kc-outline"><i class="fas fa-arrow-left me-2"></i>Go Back</a>
@endsection
