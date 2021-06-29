@extends('layouts.page')

@section('title', 'Edit - ' . $song->title)
@section('page-title', $song->title)

@section('page-content')

    {{ Form::open( [ 'route' => ['songs.show', $song->id], 'method' => 'put' ] ) }}

    <div class="row">
        <div class="card-md-6">

            <div class="card">
                <h3 class="card-header h4">Edit Song</h3>

                <div class="card-body">
                    <div class="form-group">
                        <x-inputs.text label="Song Title" id="title" name="title" :value="old('title', $song->title)" />
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Category</legend>
                        @foreach($categories as $cat)
                            <x-inputs.checkbox
                                label="{{ $cat->title }}"
                                id="categories_{{ $cat->id }}"
                                name="categories[]"
                                value="{{ $cat->id }}"
                                inline="true"
                                :checked="checkbox_group_old('categories', $cat->id, $song->categories->pluck('id')->all())"
                            />
                        @endforeach
                    </fieldset>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Status</legend>
                        <div id="status" class="btn-group btn-group-mobile-vertical btn-group-toggle bg-white" data-toggle="buttons">
                        @foreach($statuses as $status)
                            <label
                                for="status_{{ $status->id }}"
                                class="btn btn-outline-{{ $status->colour }} btn-radio py-1 px-3 text-left d-flex align-items-center {{ radio_old('status', $status->id, '', $song->status->id) ? 'active' : '' }}"
                            >
                                @if('Pending' === $status->title)
                                    <i class="fas fa-fw fa-lock mr-2"></i>
                                @endif
                                <span>
                                    <input
                                        id="status_{{ $status->id }}"
                                        name="status"
                                        value="{{ $status->id }}"
                                        type="radio"
                                        autocomplete="off"
                                        {{ radio_old('status', $status->id, '', $song->status->id) ? 'checked' : '' }}
                                    >
                                    <span>{{ $status->title }}</span>
                                </span>
                            </label>
                        @endforeach
                        </div>

                        <div class="text-muted mt-1">Songs are hidden from general members when they are "Pending".</div>

                    </fieldset>

                    <div class="form-group">
                        <x-inputs.select
                            label="Pitch Blown"
                            id="pitch_blown"
                            name="pitch_blown"
                            :options="$pitches"
                            :selected="old('pitch_blown', $song->pitch_blown)"
                        />
                    <div class="form-group">
                    </div>

                    {{--
                    <hr>

                    <h3>Attachments</h3>

                    <h4>Add Attachment</h4>
                    <p>
                        {{ Form::label('attachment-title', 'Title') }}
                        {{ Form::text('attachment-title', '', ['class' => 'form-control']) }}
                    </p>
                    <p>
                        {{ Form::label('attachment-category', 'Category') }}
                        {{ Form::select('attachment-category', ['Learning Track', 'Demo', 'Sheet Music'], '', ['class' => 'custom-select form-control-sm']) }}
                    </p>
                    <p>
                        {{ Form::label('attachment-upload', 'Upload Attachment') }}
                        {{ Form::text('attachment-upload', '', ['class' => 'form-control']) }}
                    </p>
                    --}}

                    <div class="form-group">
                        <x-inputs.checkbox
                            :label='"Send \"Song Updated\" notification"'
                            id="send_notification"
                            name="send_notification"
                            value="true"
                            :checked="checkbox_old('send_notification', true)"
                        />
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('songs.show', [$song]) }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>

        </div>
    </div>
    
    {{ Form::close() }}

@endsection