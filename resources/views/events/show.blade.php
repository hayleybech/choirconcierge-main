@extends('layouts.app')

@section('title', $event->title . ' - Events')

@section('content')
    
    <!--suppress VueDuplicateTag -->
    <h2 class="display-4 mb-4">{{$event->title}} <a href="{{route( 'event.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

    @include('partials.flash')

    <div class="event-summary">
        <p class="mb-2 text-muted">
            Event Type: {{ $event->type->title }}
        </p>

        <p class="mb-2 text-muted">
            Start Date: {{ $event->start_date }}
        </p>
        <p class="mb-2 text-muted">
            Location: {{ $event->location }}
        </p>
    </div>

    <div class="event-map google-maps">
        <iframe
                width="600"
                height="450"
                frameborder="0" style="border:0"
                src="https://www.google.com/maps/embed/v1/place?key=<?= env('API_GOOGLE_KEY') ?>&q=<?= urlencode($event->location)?>"
                allowfullscreen>
        </iframe>
    </div>


@endsection