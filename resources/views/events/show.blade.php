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

            <h4 class="mt-2">My RSVP</h4>
            @if($my_rsvp)
                @if($event->isUpcoming())
                    <inline-edit-field action="{{ route('events.rsvps.update', ['event' => $event, 'rsvp' => $my_rsvp]) }}" value="{{ $my_rsvp->response_string }}" csrf="{{ csrf_token() }}">
                        <label for="rsvp_response" class="d-block">Will you attend?</label>

                        <div class="custom-control custom-radio custom-control-inline">
                            <input id="rsvp_response_yes" name="rsvp_response" value="yes" class="custom-control-input" type="radio" {{ 'yes' === $my_rsvp->response ? 'checked' : '' }}>
                            <label for="rsvp_response_yes" class="custom-control-label">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input id="rsvp_response_maybe" name="rsvp_response" value="maybe" class="custom-control-input" type="radio" {{ 'maybe' === $my_rsvp->response ? 'checked' : '' }}>
                            <label for="rsvp_response_maybe" class="custom-control-label">Maybe</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input id="rsvp_response_no" name="rsvp_response" value="no" class="custom-control-input" type="radio" {{ 'no' === $my_rsvp->response ? 'checked' : '' }}>
                            <label for="rsvp_response_no" class="custom-control-label">No</label>
                        </div>
                    </inline-edit-field>
                @else
                    {{ $my_rsvp->response_string }}
                @endif
            @elseif($event->isUpcoming())
            {{ Form::open(['route' => ['events.rsvps.store', $event->id]]) }}
                <div class="form-group">
                    <label for="rsvp_response" class="d-block">Will you attend?</label>

                    <div class="custom-control custom-radio custom-control-inline">
                        <input id="rsvp_response_yes" name="rsvp_response" value="yes" class="custom-control-input" type="radio">
                        <label for="rsvp_response_yes" class="custom-control-label">Yes</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input id="rsvp_response_maybe" name="rsvp_response" value="maybe" class="custom-control-input" type="radio" checked>
                        <label for="rsvp_response_maybe" class="custom-control-label">Maybe</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input id="rsvp_response_no" name="rsvp_response" value="no" class="custom-control-input" type="radio">
                        <label for="rsvp_response_no" class="custom-control-label">No</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="far fa-fw fa-check"></i> Save RSVP</button>
                </div>
            {{ Form::close() }}
            @else
                You didn't RSVP for this event.
            @endif
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
<script>
    import InlineEditField from "../../assets/js/components/InlineEditField";
    export default {
        components: {InlineEditField}
    }
</script>