@extends('layouts.public')

@section('title', isset($pageTitle) ? $pageTitle . ' - Armely' : 'Partner - Armely')

@section('content')
    <div class="partner-page-wrapper">
        {!! $content !!}
    </div>
@endsection
