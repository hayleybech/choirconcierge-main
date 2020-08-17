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
        <div class="card-tabs nav nav-tabs">
            <a href="#pane-all" class="card-tab nav-link" id="tab-all" data-toggle="tab">All</a>
            <a href="#pane-active" class="card-tab nav-link active" id="tab-active" data-toggle="tab">Active</a>
            <a href="#pane-learning" class="card-tab nav-link" id="tab-learning" data-toggle="tab">Learning</a>
            <a href="#pane-pending" class="card-tab nav-link" id="tab-pending" data-toggle="tab">Pending</a>
            <a href="#pane-archived" class="card-tab nav-link" id="tab-archived" data-toggle="tab">Archived</a>
        </div>

        <div class="tab-content">
            <div class="tab-pane" id="pane-all" role="tabpanel" aria-labelledby="tab-all">
                @include('songs.table', ['songs' => $all_songs, 'col_status' => true])
            </div>
            <div class="tab-pane active" id="pane-active" role="tabpanel" aria-labelledby="tab-active">
                @include('songs.table', ['songs' => $active_songs, 'col_status' => false])
            </div>
            <div class="tab-pane" id="pane-learning" role="tabpanel" aria-labelledby="tab-learning">
                @include('songs.table', ['songs' => $learning_songs, 'col_status' => false])
            </div>
            <div class="tab-pane" id="pane-pending" role="tabpanel" aria-labelledby="tab-pending">
                @include('songs.table', ['songs' => $pending_songs, 'col_status' => false])
            </div>
            <div class="tab-pane" id="pane-archived" role="tabpanel" aria-labelledby="tab-archived">
                @include('songs.table', ['songs' => $archived_songs, 'col_status' => false])
            </div>
        </div>

    </div>

@endsection