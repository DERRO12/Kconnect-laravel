@extends('errors.layout')

@section('title', 'Under Maintenance')
@section('code', '503')
@section('icon', 'fa-wrench')
@section('heading', "We'll be back soon")
@section('message', 'K-Connect is undergoing scheduled maintenance. Please check back in a few minutes.')

@section('actions')
    <a href="javascript:location.reload()" class="btn btn-kc-outline"><i class="fas fa-sync-alt me-2"></i>Try Again</a>
@endsection
