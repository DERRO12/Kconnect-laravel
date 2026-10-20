@extends('errors.layout')

@section('title', 'Session Expired')
@section('code', '419')
@section('icon', 'fa-hourglass-end')
@section('heading', 'Your session has expired')
@section('message', 'The page was open for too long, so the form could not be submitted. Please go back, reload the page and try again.')

@section('actions')
    @php
        // Back to the page the form was on (same site only), else the login page
        $previous = url()->previous();
        $backUrl = ($previous && str_starts_with($previous, url('/')) && $previous !== url()->current()) ? $previous : url('login');
    @endphp
    <a href="{{ $backUrl }}" class="btn btn-kc-outline"><i class="fas fa-redo me-2"></i>Back to the page</a>
@endsection
