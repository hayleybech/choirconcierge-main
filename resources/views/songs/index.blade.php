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
                        <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> Apply</button>
                        <a href="{{ route('songs.index') }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="card">
        <div class="card-tabs nav nav-tabs">
            <a href="#pane-all" class="card-tab nav-link" id="tab-all" data-toggle="tab">All <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $all_songs->count() }}</span></a>
            <a href="#pane-active" class="card-tab nav-link active text-success" id="tab-active" data-toggle="tab"><i class="fas fa-fw fa-circle mr-2"></i>Active  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $active_songs->count() }}</span></a>
            <a href="#pane-learning" class="card-tab nav-link text-warning" id="tab-learning" data-toggle="tab"><i class="fas fa-fw fa-circle mr-2"></i>Learning  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $learning_songs->count() }}</span></a>
            @can('create', \App\Models\Song::class)
            <a href="#pane-pending" class="card-tab nav-link text-danger" id="tab-pending" data-toggle="tab"><i class="fas fa-fw fa-lock"></i>Pending  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $pending_songs->count() }}</span></a>
            @endcan
            <a href="#pane-archived" class="card-tab nav-link text-secondary" id="tab-archived" data-toggle="tab"><i class="fas fa-fw fa-circle mr-2"></i>Archived  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $archived_songs->count() }}</span></a>
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
            @can('create', \App\Models\Song::class)
            <div class="tab-pane" id="pane-pending" role="tabpanel" aria-labelledby="tab-pending">
                @include('songs.table', ['songs' => $pending_songs, 'col_status' => false])
            </div>
            @endcan
            <div class="tab-pane" id="pane-archived" role="tabpanel" aria-labelledby="tab-archived">
                @include('songs.table', ['songs' => $archived_songs, 'col_status' => false])
            </div>
        </div>

    </div>

@endsection