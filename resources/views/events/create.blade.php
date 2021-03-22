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
                        {{ Form::label('title', 'Event Title') }}
                        {{ Form::text('title', '', ['class' => 'form-control']) }}
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Type</legend>
                        @foreach($types as $type)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="type_{{$type->id}}" name="type" value="{{$type->id}}" class="custom-control-input" type="radio">
                                <label for="type_{{$type->id}}" class="custom-control-label">{{$type->title}}</label>
                            </div>
                        @endforeach
                    </fieldset>

                    <div class="form-group">
                        {{ Form::label('date_range', 'Event Date') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
                            </div>
                            {{ Form::text('date_range', '', ['class' => 'form-control events-date-range-picker']) }}
                            {{ Form::hidden('start_date', '', ['class' => 'start-date-hidden']) }}
                            {{ Form::hidden('end_date', '', ['class' => 'end-date-hidden']) }}

                        </div>
                        <p><small class="text-muted">Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</small></p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('call_time', 'Call Time') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                            </div>
                            {{ Form::text('call_time_input', '', ['class' => 'form-control events-single-date-time-picker']) }}
                            {{ Form::hidden('call_time', '', ['class' => 'date-time-hidden']) }}
                        </div>
                    </div>

                <!--
    <div class="form-group">
        {{ Form::label('call_time_hr', 'Call Time') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                                <span class="input-group-text call-time-date">January 1, 2000</span>
                            </div>
{{ Form::select('call_time_hr', [
                    '01' => '01',
                    '02' => '02',
                    '03' => '03',
                    '04' => '04',
                    '05' => '05',
                    '06' => '06',
                    '07' => '07',
                    '08' => '08',
                    '09' => '09',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                ], '', ['class' => 'custom-select call-time-hr']) }}
                {{ Form::select('call_time_min', [
                        '00' => '00',
                        '15' => '15',
                        '30' => '30',
                        '45' => '45',
                    ], '', ['class' => 'custom-select call-time-min']) }}
                {{ Form::select('call_time_ampm', [
                        'AM' => 'AM',
                        'PM' => 'PM'
                    ], '', ['class' => 'custom-select time-ampm']) }}
                {{ Form::text('call_time', '', ['class' => 'date-time-hidden']) }}
                        </div>
                    </div>-->

                    <div class="form-group">
                        {{ Form::label('', 'Repeating Event') }}
                        <x-inputs.switch label="Repeat?" name="is_repeating" value="1"></x-inputs.switch>
                    </div>

                    <fieldset id="repeat_details" style="padding: 15px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; margin-bottom: 10px;">

                        <div class="form-group">
                            {{ Form::label('repeat_frequency_unit', 'Repeat every') }}<br>

                            @foreach(['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $unit)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input id="repeat_frequency_unit_{{$key}}" name="repeat_frequency_unit" value="{{$key}}" class="custom-control-input" type="radio">
                                    <label for="repeat_frequency_unit_{{$key}}" class="custom-control-label">{{$unit}}</label>
                                </div>
                            @endforeach

                        </div>

                        <div class="form-group">
                            {{ Form::label('repeat_until', 'Repeat until') }}
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
                                </div>
                                {{ Form::text('repeat_until_input', '', ['class' => 'form-control events-single-date-picker']) }}
                                {{ Form::hidden('repeat_until', '', ['class' => 'date-time-hidden']) }}
                            </div>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= config('services.google.key') ?>&libraries=places&callback=initMap" async defer></script>

    <script src="{{ global_asset('js/events.js') }}"></script>
    @endpush

@endsection