@extends('layouts.public')

@section('title', ($industryPage['label'] ?? 'Industry') . ' | Armely')
@section('meta_description', $industryPage['description'] ?? 'Explore Armely industry solutions.')
@section('canonical_url', url('/industries/' . $industrySlug))

@section('content')
    @include($industryView)
@endsection
