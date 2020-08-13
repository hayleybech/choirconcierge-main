@extends('layouts.page-blank')

@section('title', $event->title . ' - Events')

@section('page-content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start">
            <h1 class="h2 mb-0">{{ $event->title }}</h1>
            @can('update', $event)
                <a href="{{route( 'events.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-light flex-shrink-0"><i class="fa fa-fw fa-edit"></i> Edit</a>
            @endcan
        </div>
        <div class="card-body">
            <div class="badge badge-pill badge-secondary">{{ $event->type->title }}</div>
            <div><time class="font-weight-bold">{{ $event->start_date->format('M d, H:i') }}</time> to <time class="font-weight-bold">{{ $event->end_date->format('M d, H:i') }}</time></div>
            <div>Call Time: <time>{{ $event->call_time->format('M d, H:i') }}</time></div>
            <div>{{ $event->description }}</div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-header h4">Location</h3>

        <div class="card-body">
            <p>
                <span style="background-image: url('{{ $event->location_icon }}');" class="place-icon"></span> <span class="place-name">{{ $event->location_name }}</span> <br>
                <span class="place-address">{{ $event->location_address }}</span>
            </p>

            <div class="event-map google-maps">
                <iframe
                        width="600"
                        height="450"
                        frameborder="0" style="border:0"
                        src="https://www.google.com/maps/embed/v1/place?key=<?= env('API_GOOGLE_KEY') ?>&q=place_id:<?= urlencode($event->location_place_id)?>"
                        allowfullscreen>
                </iframe>
            </div>

        </div>
    </div>



@endsection