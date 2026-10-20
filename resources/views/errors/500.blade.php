@extends('errors.layout')

@section('title', $errorTitle ?? 'Server Error')
@section('code', '500')
@section('icon', 'fa-tools')
@section('heading', $errorTitle ?? 'Something went wrong')
@section('message', $errorMessage ?? 'An unexpected error occurred on our side. Please try again in a moment. If the problem continues, contact IT support.')

@section('detail')
    @if (!empty($errorDetail))
        <div class="error-detail"><i class="fas fa-info-circle me-2"></i>{{ $errorDetail }}</div>
    @endif
@endsection

@section('actions')
    @if (!empty($backUrl))
        <a href="{{ $backUrl }}" class="btn btn-kc-outline"><i class="fas fa-arrow-left me-2"></i>{{ $backLabel ?? 'Go Back' }}</a>
    @else
        <a href="javascript:history.back()" class="btn btn-kc-outline"><i class="fas fa-arrow-left me-2"></i>Go Back</a>
    @endif
@endsection
