@extends('layouts.page')

@section('title', 'Add Song')
@section('page-title', 'Add Song')

@section('page-content')

    {{ Form::open( [ 'route' => 'songs.index' ] ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">Song Details</h3>

                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('title', 'Song Title') }}
                        {{ Form::text('title', '', ['class' => 'form-control']) }}
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Category</legend>
                        @foreach($categories as $cat)
                            <x-inputs.checkbox label="{{ $cat->title }}" id="categories_{{ $cat->id }}" name="categories[]" value="{{ $cat->id }}" inline="true"></x-inputs.checkbox>
                        @endforeach
                    </fieldset>


                    <fieldset class="form-group">
                        <legend class="col-form-label">Status</legend>
                        <div id="status" class="btn-group btn-group-mobile-vertical btn-group-toggle bg-white" data-toggle="buttons">
                        @foreach($statuses as $status)
                            <label for="status_{{ $status->id }}" class="btn btn-outline-{{ $status->colour }} btn-radio p1-3 px-3 text-left d-flex align-items-center">
                                @if('Pending' === $status->title)
                                    <i class="fas fa-fw fa-lock mr-2"></i>
                                @endif
                                <span>
                                <input id="status_{{ $status->id }}" name="status" value="{{ $status->id }}" type="radio" autocomplete="off">
                                <span>{{ $status->title }}</span>
                            </span>
                            </label>
                        @endforeach
                        </div>
                        <div class="text-muted mt-1">Songs are hidden from general members when they are "Pending".</div>
                    </fieldset>

                    <div class="form-group">
                        {{ Form::label('pitch_blown', 'Pitch Blown') }}
                        {{ Form::select('pitch_blown',
                            $pitches,
                            '',
                            ['class' => 'custom-select']
                        ) }}
                    </div>

                    <div class="form-group">
                        <x-inputs.checkbox :label='"Send \"New Song\" notification"' id="send_notification" name="send_notification" value="true" :checked="true"></x-inputs.checkbox>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Create
                    </button>
                    <a href="{{ route('songs.index') }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>
            </div>
            
        </div>
    </div>


    {{ Form::close() }}

@endsection