@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

    <h2 class="mb-4">Songs <a href="{{route( 'song.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-user-plus"></i> Add New</a></h2>

    @if (session('status'))
        <div class="alert {{ isset($Response->error) || session('fail') ? 'alert-danger' : 'alert-success' }}" role="alert">
            {{ session('status') }}

            @isset( $Response->error )
                <pre>
			{{ var_dump($Response) }}
			@ json($args)
		</pre>
            @endisset
        </div>
    @endif

    {{--
    <form method="get" class="form-inline mb-4">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <label for="filter_category" class="input-group-text">Category</label>
            </div>
            @php
                //echo Form::select('filter_category', $categories_keyed,
                //$category, ['class' => 'custom-select form-control-sm']);
            @endphp

            <div class="input-group-append">
                <input type="submit" value="Filter" class="btn btn-secondary btn-sm">
            </div>
        </div>
    </form>--}}

    <h3>{{-- $categories_keyed[$category] --}}</h3>
    {{--@if ( $categories_keyed[$category] == 'Members')
    <p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
    @endif--}}
    <div class="r-table r-table--card-view-mobile">
        <div class="r-table__thead">
            <div class="r-table__row">
                <div class="r-table__heading column--mark"><input type="checkbox"></div>
                <div class="r-table__heading column--title">Title</div>
                <div class="r-table__heading column--status">Status</div>
                <div class="r-table__heading column--category">Category</div>
                <div class="r-table__heading column--pitch">Pitch</div>
                <div class="r-table__heading column--created">Created</div>
                <div class="r-table__heading column--actions">Actions</div>
            </div>
        </div>
        <div class="r-table__tbody">
            @each('partials.songrow', $songs, 'song', 'partials.noresults')
        </div>
    </div>

@endsection