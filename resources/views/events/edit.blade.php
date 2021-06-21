<?php /** @var App\Models\Event $event */ ?>
@extends('layouts.page')

@section('title', 'Edit - ' . $event->title)
@section('page-title')
    {{ $event->title }}
    @if( $event->is_repeating )
        <i class="fal fa-fw fa-repeat" title="Repeating Event"></i>
    @endif
@endsection

@section('page-content')

    @if($event->is_repeating)
        {{ Form::open( [ 'route' => ['events.update-recurring', $event->id, request()->query('mode')], 'method' => 'put' ] ) }}
    @else
        {{ Form::open( [ 'route' => ['events.update', $event->id], 'method' => 'put' ] ) }}
    @endif

    <input type="hidden" name="edit_mode" value="{{ request()->query('mode') }}">

    @if($event->is_repeating)
        @if('single' === request()->query('mode'))
        <x-alert variant="warning" icon="fa-calendar-day" title="Editing only this event">
            All other events in the series will remain the same.
        </x-alert>
        @elseif('following' === request()->query('mode'))
        <x-alert variant="warning" icon="fa-calendar-week" title="Editing following events">
            This and all the following events will be changed.<br>
            <strong>Changes to future events may be lost, including RSVPs, if dates are changed.</strong>
        </x-alert>
        @elseif('all' === request()->query('mode'))
        <x-alert variant="warning" icon="fa-calendar-alt" title="Editing all events">
            All events in the series will be changed.<br>
            <strong>Changes to other events may be lost, including RSVPs and attendance records, if dates are changed.</strong>
        </x-alert>
        @endif
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Edit Event</h3>

                <div class="card-body">
                    <div class="form-group">
                        <x-inputs.text label="Event Title" id="title" name="title" :value="old('title', $event->title)" />
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Type</legend>
                        @foreach($types as $type)
                            <x-inputs.radio
                                label="{{ $type->title }}"
                                id="type_id_{{ $type->id }}"
                                name="type_id"
                                value="{{ $type->id }}"
                                inline="true"
                                :checked="old('type_id', $event->type->id) == $type->id"
                            />
                        @endforeach
                    </fieldset>

                    <div class="form-group">
                        <div v-if="loading">
                            <i class="fas fa-fw fa-compact-disc fa-spin"></i>
                        </div>
                        <div v-else>
                            <event-dates
                                init-start-date="{{ old('start_date', $event->start_date) }}"
                                init-end-date="{{ old('end_date', $event->end_date) }}"
                                init-call-time="{{ old('call_time', $event->call_time) }}"
                            >
                                <template #description>Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</template>
                            </event-dates>
                        </div>
                    </div>

                    @if('single' !== request()->query('mode'))

                    {{ Form::label('', 'Repeating Event') }}

                    <toggleable-input label="Repeat?" name="is_repeating" :start-open="@json((bool) old('is_repeating', $event->is_repeating))">
                        <fieldset id="repeat_details" style="padding: 15px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; margin-bottom: 10px;">

                            <div class="form-group">
                                {{ Form::label('repeat_frequency_unit', 'Repeat every') }}<br>

                                @foreach(['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $unit)
                                    <x-inputs.radio
                                        label="{{ $unit }}"
                                        id="repeat_frequency_unit_{{ $key }}"
                                        name="repeat_frequency_unit"
                                        value="{{ $key }}"
                                        inline="true"
                                        :checked="old('repeat_frequency_unit', $event->repeat_frequency_unit) == $key"
                                    />
                                @endforeach
                            </div>

                            <div class="form-group">
                                <date-input
                                    label="Repeat until"
                                    input-name="repeat_until_input"
                                    output-name="repeat_until"
                                    :value="'{{ old('repeat_until_input', $event->repeat_until) }}'"
                                />
                            </div>

                        </fieldset>
                    </toggleable-input>

                    @endif

                    <div class="form-group location-input-wrapper">
                        <location-input
                            label="Location"
                            input-name="location"
                            location-name="{{ old('location_name', $event->location_name) }}"
                            location-place-id="{{ old('location_place_id', $event->location_place_id) }}"
                            location-icon="{{ old('location_icon', $event->location_icon) }}"
                            location-address="{{ old('location_address', $event->location_address) }}"
                            api-key="{{ config('services.google.key') }}"
                        />
                    </div>

                    <div class="form-group">
                        {{ Form::label('description', 'Description') }}
                        <limited-textarea field-id="description" field-name="description" value="{{ old('description', $event->description) }}" :maxlength="5000"></limited-textarea>
                    </div>

                    <div class="form-group">
                        <x-inputs.checkbox
                            :label='"Send \"Event Updated\" Notification"'
                            id="send_notification"
                            name="send_notification"
                            value="true"
                            :checked="! empty(old()) ? (bool) old('send_notification') : true"
                        />
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('events.show', [$event]) }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>

        </div>
    </div>

    <p><small class="text-muted">Choir's Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</small></p>

    {{ Form::close() }}

@endsection