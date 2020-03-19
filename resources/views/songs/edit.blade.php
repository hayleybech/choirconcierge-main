@extends('layouts.page')

@section('title', 'Edit - ' . $song->title)
@section('page-title', $song->title)

@section('page-content')

    {{ Form::open( array( 'route' => ['songs.show', $song->id], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Edit Song</h3>

        <div class="card-body">
            <div class="form-group">
                {{ Form::label('title', 'Song Title') }}
                {{ Form::text('title', $song->title, array('class' => 'form-control')) }}
            </div>

            <fieldset class="form-group">
                <legend class="col-form-label">Category</legend>
                @foreach($categories as $cat)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input id="categories_{{$cat->id}}" name="categories[]" value="{{$cat->id}}" class="custom-control-input" type="checkbox" {{ ( $song->categories->contains($cat->id) ) ? 'checked' : '' }}>
                        <label for="categories_{{$cat->id}}" class="custom-control-label">{{$cat->title}}</label>
                    </div>
                @endforeach
            </fieldset>


            <fieldset class="form-group">
                <legend class="col-form-label">Status</legend>
                @foreach($statuses as $status)
                    <div class="custom-control custom-radio custom-control-inline">
                        <input id="status_{{$status->id}}" name="status" value="{{$status->id}}" class="custom-control-input" type="radio" {{ ($song->status->id === $status->id ) ? 'checked' : '' }}>
                        <label for="status_{{$status->id}}" class="custom-control-label">{{$status->title}}</label>
                    </div>
                @endforeach
            </fieldset>

            <div class="form-group">
                {{ Form::label('pitch_blown', 'Pitch Blown') }}
                {{ Form::select('pitch_blown',
                    $pitches,
                    $song->pitch_blown,
                    ['class' => 'custom-select']
                ) }}
            </div>

            {{--
            <hr>

            <h3>Attachments</h3>

            <h4>Add Attachment</h4>
            <p>
                {{ Form::label('attachment-title', 'Title') }}
                {{ Form::text('attachment-title', '', array('class' => 'form-control')) }}
            </p>
            <p>
                {{ Form::label('attachment-category', 'Category') }}
                {{ Form::select('attachment-category', array('Learning Track', 'Demo', 'Sheet Music'), '', ['class' => 'custom-select form-control-sm']) }}
            </p>
            <p>
                {{ Form::label('attachment-upload', 'Upload Attachment') }}
                {{ Form::text('attachment-upload', '', array('class' => 'form-control')) }}
            </p>
            --}}

        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection