@extends('layouts.app')

@section('title', 'Events')

@section('content')

    <div class="jumbotron bg-light">
        <h2 class="display-4">Events <a href="{{route( 'event.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-calendar-plus"></i> Add New</a></h2>
    </div>


    @include('partials.flash')

    <form method="get" class="form-inline mb-0">
        @foreach( $filters as $filter )
            <div class="input-group input-group-sm mb-2 mr-2">
                <div class="input-group-prepend">
                    @php
                        $label_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary bg-primary text-white' : 'bg-light';
                    @endphp
                    <label for="{{ $filter['name']}} " class="input-group-text {{$label_class}}">{{ $filter['label'] }}</label>
                </div>
                @php
                    $field_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary' : '';
                    echo Form::select($filter['name'],
                        $filter['list'],
                        $filter['current'],
                        ['class' => 'custom-select form-control-sm ' . $field_class]
                    );
                @endphp
            </div>
        @endforeach

        <div class="input-group input-group-sm mb-2 mr-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <a href="{{ route('events.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
            </div>
        </div>
    </form>

    <div class="r-table r-table--card-view-mobile">
        <div class="r-table__thead">
            <div class="r-table__row">
                <div class="r-table__heading column--mark"><input type="checkbox"></div>
                <div class="r-table__heading column--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                <div class="r-table__heading column--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                <div class="r-table__heading column--start-date">Event Date</div>
                <div class="r-table__heading column--location">Location</div>
                <div class="r-table__heading column--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                <div class="r-table__heading column--actions">Actions</div>
            </div>
        </div>
        <div class="r-table__tbody">
            @each('events.index_row', $events, 'event', 'partials.noresults')
        </div>
    </div>

@endsection