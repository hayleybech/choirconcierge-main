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
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input id="categories_{{$cat->id}}" name="categories[]" value="{{$cat->id}}" class="custom-control-input" type="checkbox">
                                <label for="categories_{{$cat->id}}" class="custom-control-label">{{$cat->title}}</label>
                            </div>
                        @endforeach
                    </fieldset>


                    <fieldset class="form-group">
                        <legend class="col-form-label">Status</legend>
                        @foreach($statuses as $status)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="status_{{$status->id}}" name="status" value="{{$status->id}}" class="custom-control-input" type="radio">
                                <label for="status_{{$status->id}}" class="custom-control-label">{{$status->title}}</label>
                            </div>
                        @endforeach
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
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input id="suppress_email" name="suppress_email" value="yes" class="custom-control-input" type="checkbox">
                            <label for="suppress_email" class="custom-control-label">Suppress "New Song" notification</label>
                        </div>
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