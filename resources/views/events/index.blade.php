@extends('layouts.page')

@section('title', 'Events')
@section('page-title')
<i class="fal fa-fw fa-calendar"></i> Events
@endsection

@section('page-action')
    @if(Auth::user()->hasRole('Music Team'))
    <a href="{{route( 'event.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-calendar-plus"></i> Add New</a>
    @endif
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Events List</h3>

        <div class="card-body">

            <?php
            use App\Models\Event;
            $filters_class = Event::hasActiveFilters() ? 'btn-primary' : 'btn-outline-secondary';
            ?>
            <a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>

            <div class="collapse mt-2" id="filters">

                <form method="get" class="form-inline mb-0">
                    @each('partials.filter', $filters, 'filter')

                    <div class="input-group input-group-sm mb-2 mr-2">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> Apply</button>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
                        </div>
                    </div>
                </form>

            </div>

        </div>

        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--event">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading event-col--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading event-col--date">Event Date</div>
                    <div class="r-table__heading event-col--location">Location</div>
                    <div class="r-table__heading col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('events.index_row', $events, 'event', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $events->count() }} events
        </div>

    </div>

@endsection