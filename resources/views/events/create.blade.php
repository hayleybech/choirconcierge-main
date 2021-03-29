@extends('layouts.page')

@section('title', 'Add Event')
@section('page-title', 'Add Event')

@section('page-content')

    {{ Form::open( [ 'route' => 'events.index' ] ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">Event Details</h3>

                <div class="card-body">

                    <div class="form-group">
                        <x-inputs.text label="Event Title" id="title" name="title"></x-inputs.text>
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Type</legend>
                        @foreach($types as $type)
                            <x-inputs.radio label="{{ $type->title }}" id="type_{{ $type->id }}" name="type" value="{{ $type->id }}" inline="true"></x-inputs.radio>
                        @endforeach
                    </fieldset>

                    <div class="form-group">
                        <div v-if="loading">
                            <i class="fas fa-fw fa-compact-disc fa-spin"></i>
                        </div>
                        <div v-else>
                            <event-dates>
                                <template #description>Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</template>
                            </event-dates>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('', 'Repeating Event') }}
                        <x-inputs.switch label="Repeat?" name="is_repeating" value="1"></x-inputs.switch>
                    </div>

                    <fieldset id="repeat_details" style="padding: 15px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; margin-bottom: 10px;">

                        <div class="form-group">
                            {{ Form::label('repeat_frequency_unit', 'Repeat every') }}<br>

                            @foreach(['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $unit)
                                <x-inputs.radio label="{{ $unit }}" id="repeat_frequency_unit_{{ $key }}" name="repeat_frequency_unit" value="{{ $key }}" inline="true"></x-inputs.radio>
                            @endforeach

                        </div>

                        <div class="form-group">
                            <input-datetime label="Repeat until" input-name="repeat_until_input" output-name="repeat_until"></input-datetime>
                        </div>

                    </fieldset>


                    <div class="form-group location-input-wrapper">
                        {{ Form::label('location', 'Location') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
                            </div>
                            {{ Form::text('location', '', ['class' => 'form-control location-input', 'rows' => '3']) }}
                            {{ Form::hidden('location_place_id', '', ['class' => 'form-control location-place-id']) }}
                            {{ Form::hidden('location_icon', '', ['class' => 'form-control location-icon']) }}
                            {{ Form::hidden('location_name', '', ['class' => 'form-control location-name']) }}
                            {{ Form::hidden('location_address', '', ['class' => 'form-control location-address']) }}
                        </div>
                        <small class="location-place form-text text-muted">
                            <span class="place-icon"></span>
                            <span class="place-name"></span>
                            <span class="place-address"></span>
                        </small>
                    </div>

                    <div class="form-group">
                        {{ Form::label('description', 'Description') }}
                        <limited-textarea field-id="description" field-name="description" value="" :maxlength="5000"></limited-textarea>
                    </div>

                    <div class="form-group">
                        <x-inputs.checkbox :label='"Send \"Event Created\" Notification"' id="send_notification" name="send_notification" value="true" :checked="true"></x-inputs.checkbox>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Create
                    </button>
                    <a href="{{ route('events.index') }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>
            
        </div>
    </div>

    <p><small class="text-muted">Choir's Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</small></p>


    {{ Form::close() }}

    @push('scripts-footer-bottom')
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= config('services.google.key') ?>&libraries=places&callback=initMap" async defer></script>

    <script src="{{ global_asset('js/events.js') }}"></script>
    @endpush

@endsection