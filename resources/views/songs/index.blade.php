@extends('layouts.page')

@section('title', 'Songs')
@section('page-title', 'Songs')
@section('page-action')
    <a href="{{route( 'song.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-plus"></i> Add New</a>
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Songs List</h3>

        <div class="card-body">

            <form method="get" class="form-inline mb-0">
                @each('partials.filter', $filters, 'filter')

                <div class="input-group input-group-sm mb-2 mr-2">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('songs.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--song">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading song-col--status"><a href="{{ $sorts['status.title']['url'] }}">Status<i class="fa fas sort-{{ $sorts['status.title']['dir'] }} {{ ($sorts['status.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading song-col--category">Category</div>
                    <div class="r-table__heading song-col--pitch">Pitch</div>
                    <div class="r-table__heading col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('songs.index_row', $songs, 'song', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $songs->count() }} songs
        </div>

    </div>

@endsection