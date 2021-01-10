@extends('layouts.page')

@section('title', 'Events')
@section('page-title')
    <i class="fal fa-fw fa-calendar"></i> Events
    @can('create', \App\Models\Event::class)
        <a href="{{route( 'events.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-calendar-plus"></i> Add New</a>
    @endcan
@endsection

@section('page-action')
    <?php
    use App\Models\Event;
    $filters_class = Event::hasActiveFilters() ? 'btn-primary' : 'btn-light';
    ?>
    <a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>

    @can('viewAny', \App\Models\Attendance::class)
        <a href="{{route( 'events.reports.attendance' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-analytics"></i> View Attendance Report</a>
    @endcan
@endsection

@section('page-content')

    <div class="d-flex justify-content-end">
        <div class="collapse mt-2" id="filters">

            <form method="get" class="form-inline mb-0">
                @each('partials.filter', $filters, 'filter')

                <div class="input-group input-group-sm mb-2 mr-2">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> Apply</button>
                        <a href="{{ route('events.index') }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-tabs nav nav-tabs">
            <a href="#pane-all" class="card-tab nav-link" id="tab-all" data-toggle="tab">All  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $all_events->count() }}</span></a>
            <a href="#pane-upcoming" class="card-tab nav-link active" id="tab-upcoming" data-toggle="tab">Upcoming  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $upcoming_events->count() }}</span></a>
            <a href="#pane-past" class="card-tab nav-link" id="tab-past" data-toggle="tab">Past  <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $past_events->count() }}</span></a>
        </div>

        <div class="tab-content">
            <div class="tab-pane" id="pane-all" role="tabpanel" aria-labelledby="tab-all">
                @include('events.table', ['events' => $all_events, 'col_attendance' => false, 'col_rsvp' => false])
            </div>
            <div class="tab-pane active" id="pane-upcoming" role="tabpanel" aria-labelledby="tab-upcoming">
                @include('events.table', ['events' => $upcoming_events, 'col_attendance' => false, 'col_rsvp' => true])
            </div>
            <div class="tab-pane" id="pane-past" role="tabpanel" aria-labelledby="tab-past">
                @include('events.table', ['events' => $past_events, 'col_attendance' => true, 'col_rsvp' => false])
            </div>
        </div>

    </div>

@endsection