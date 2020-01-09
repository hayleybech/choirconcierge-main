@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

    <h2 class="display-4">{{$song->title}}</h2>

    <p class="mb-2 text-muted">
        Status: {{ $song->status->title }}
    </p>
    <p class="mb-2 text-muted">
        Category:
        <ul class="list-inline">
        @foreach( $song->categories as $cat )
        <li class="list-inline-item">{{ $cat->title }}</li>
        @endforeach
        </ul>
    </p>
    <p class="mb-2 text-muted">
        Pitch Blown: {{ $song->getPitchBlown() }}
    </p>

@endsection