@extends('layouts.app')

@section('title', $event->title . ' - Events')

@section('content')
    
    <!--suppress VueDuplicateTag -->

    <div class="jumbotron bg-light">
        <h2 class="display-4">{{$event->title}} <a href="{{route( 'event.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>
        <p class="lead">
            Event Type: {{ $event->type->title }}<br>
            Event Date: {{ $event->start_date->format('M d, H:i') }} to {{ $event->end_date->format('M d, H:i') }}<br>
            Call Time: {{ $event->call_time->format('M d, H:i') }}<br>
            Description: {{ $event->description }}
        </p>
    </div>

    @include('partials.flash')

    <div class="card bg-light">
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