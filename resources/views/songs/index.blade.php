@extends('layouts.page')

@section('title', 'Songs')
@section('page-title')
    <i class="fal fa-fw fa-list-music"></i> Songs
    @can('create', \App\Models\Song::class)
        <a href="{{route( 'songs.create' )}}" class="btn btn-add btn-sm btn-primary"><i class="fa fa-fw fa-plus"></i> Add New</a>
    @endcan
@endsection
@section('page-action')

    <?php
    use App\Models\Song;
    $filters_class = Song::hasActiveFilters() ? 'btn-primary' : 'btn-light';
    ?>

    <a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>
@endsection

@section('page-content')

    <div class="d-flex justify-content-end">

        <div class="collapse mt-2" id="filters">

            <form method="get" class="form-inline mb-0">
                @each('partials.filter', $filters, 'filter')

                <div class="input-group input-group-sm mb-2 mr-2">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> Apply</button>
                        <a href="{{ route('songs.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="card">
        <div class="card-header"></div>
        
        <table class="table card-table">
            <thead>
                <tr class="row--song">
                    <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--status"><a href="{{ $sorts['status.title']['url'] }}"><i class="fas fa-fw fa-circle mr-2 text-secondary"></i><span class="status__title">Status</span><i class="fa fas sort-{{ $sorts['status.title']['dir'] }} {{ ($sorts['status.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--category">Category</th>
                    <th class="col--pitch">Pitch</th>
                    <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--delete"></th>
                </tr>
            </thead>
            <tbody>
                @each('songs.index_row', $songs, 'song', 'partials.noresults-table')
            </tbody>
        </table>

        <div class="card-footer">
            {{ $songs->count() }} songs
        </div>

    </div>

@endsection