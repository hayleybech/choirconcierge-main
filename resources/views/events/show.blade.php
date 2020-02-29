@extends('layouts.app')

@section('title', $event->title . ' - Events')

@section('content')
    
    <!--suppress VueDuplicateTag -->
    <h2 class="display-4 mb-4">{{$event->title}} <a href="{{route( 'event.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

    @include('partials.flash')

    <p class="mb-2 text-muted">
        Event Type: {{ $event->type->title }}
    </p>

    <p class="mb-2 text-muted">
        Start Date: {{ $event->start_date }}
    </p>
    <p class="mb-2 text-muted">
        Location: {{ $event->location }}
    </p>


@endsection