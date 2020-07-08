@extends('layouts.page')

@section('title', 'Events')
@section('page-title')
    <i class="fal fa-fw fa-calendar"></i> Events
    @if(Auth::user()->hasRole('Music Team'))
        <a href="{{route( 'events.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-calendar-plus"></i> Add New</a>
    @endif
@endsection

@section('page-action')
    <?php
    use App\Models\Event;
    $filters_class = Event::hasActiveFilters() ? 'btn-primary' : 'btn-light';
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
                        <a href="{{ route('events.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <h3 class="card-header h4">Upcoming Events</h3>

        <table class="table card-table">
            <thead>
                <tr class="row--event">
                    <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--date">Event Date</th>
                    <th class="col--location">Location</th>
                    <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--delete"></th>
                </tr>
            </thead>
            <tbody>
                @each('events.index_row', $upcoming_events, 'event', 'partials.noresults')
            </tbody>
        </table>

        <div class="card-footer">
            {{ $upcoming_events->count() }} events
        </div>

    </div>

    <div class="card mb-5">
        <h3 class="card-header h4">Past Events</h3>

        <table class="table card-table">
            <thead>
                <tr class="row--event">
                    <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--date">Event Date</th>
                    <th class="col--location">Location</th>
                    <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
                    <th class="col--delete"></th>
                </tr>
            </thead>
            <tbody>
                @each('events.index_row', $past_events, 'event', 'partials.noresults')
            </tbody>
        </table>

        <div class="card-footer">
            {{ $past_events->count() }} events
        </div>

    </div>

@endsection