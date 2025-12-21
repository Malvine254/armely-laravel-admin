@extends('layouts.public')

@section('title', isset($pageTitle) ? $pageTitle . ' - Armely' : 'Partner - Armely')

@section('content')
    {!! $content !!}
@endsection
