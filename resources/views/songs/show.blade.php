@extends('layouts.app')

@section('title', 'Main menu')

@section('content')
    
    <h2 class="display-4 mb-4">{{$song->title}} <a href="{{route( 'song.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

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

    <h3>Attachments <a href="#" class="btn btn-add btn-sm force-xs btn-outline-primary"><i class="fa fa-fw fa-plus"></i> Add</a></h3>

    <div class="r-table r-table--card-view-mobile">
        <div class="r-table__thead">
            <div class="r-table__row">
                <div class="r-table__heading column--mark"><input type="checkbox"></div>
                <div class="r-table__heading column--title">Title</div>
                <div class="r-table__heading column--filename">Filename</div>
                <div class="r-table__heading column--category">Category</div>
                <div class="r-table__heading column--actions">Actions</div>
            </div>
        </div>
        <div class="r-table__tbody">
            @each('partials.attachment', $song->attachments, 'attachment', 'partials.noresults')
        </div>
    </div>

@endsection