@extends('layouts.page')

@section('title', $event->title . ' - Events')
@section('page-title', $event->title)

@section('page-action')
    @if(Auth::user()->hasRole('Music Team'))
    <a href="{{route( 'events.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endif
@endsection

@section('page-lead')
    Event Type: {{ $event->type->title }}<br>
    {{ $event->start_date->format('M d, H:i') }} to {{ $event->end_date->format('M d, H:i') }}<br>
    Call Time: {{ $event->call_time->format('M d, H:i') }}<br>
    {{ $event->description }}
@endsection

@section('page-content')

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