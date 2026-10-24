@extends('errors.layout')

@section('title', 'Access Denied')
@section('code', '403')
@section('icon', 'fa-lock')
@section('heading', 'Access denied')
@section('message', ($exception ?? null)?->getMessage() ?: 'You do not have permission to open this page.')

@section('actions')
    <a href="{{ url('/') }}" class="btn btn-kc-outline"><i class="fas fa-home me-2"></i>K-Connect Home</a>
@endsection
