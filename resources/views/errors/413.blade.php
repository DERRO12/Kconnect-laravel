@extends('errors.layout')

@section('title', 'File Too Large')
@section('code', '413')
@section('icon', 'fa-file-video')
@section('heading', 'That upload is too large')
@section('message', 'The file is bigger than this server accepts ('.ini_get('post_max_size').'). Upload a smaller file, or use a YouTube or online video link instead.')

@section('actions')
    <a href="javascript:history.back()" class="btn btn-kc-outline"><i class="fas fa-arrow-left me-2"></i>Go Back</a>
@endsection
