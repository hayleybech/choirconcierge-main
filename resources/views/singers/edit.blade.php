@extends('layouts.page')

@section('title', 'Edit - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

    {{ Form::open( array( 'route' => ['singers.show', $singer->id], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <div class="card-header">Edit Singer</div>

        <div class="card-body">
            <p>
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', $singer->name, array('class' => 'form-control')) }}
            </p>

            <p>
                {{ Form::label('email', 'E-Mail Address') }}
                {{ Form::email('email', $singer->email, array('class' => 'form-control')) }}
            </p>

            <fieldset class="form-group">
                <legend class="col-form-label">Onboarding</legend>

                <div class="custom-control custom-radio">
                    <input id="onboarding_enabled_yes" name="onboarding_enabled" value="1" class="custom-control-input" type="radio" {{ ( $singer->onboarding_enabled ) ? 'checked' : '' }}>
                    <label for="onboarding_enabled_yes" class="custom-control-label">
                        Enabled
                        <small class="text-muted ml-2">
                            Choose this option for new/prospective singers.
                        </small>
                    </label>
                </div>

                <div class="custom-control custom-radio">
                    <input id="onboarding_enabled_no" name="onboarding_enabled" value="0" class="custom-control-input" type="radio" {{ ( ! $singer->onboarding_enabled ) ? 'checked' : '' }}>
                    <label for="onboarding_enabled_no" class="custom-control-label">
                        Disabled
                        <small class="text-muted ml-2">
                            Choose this option when you're adding existing singers.
                        </small>
                    </label>
                </div>
            </fieldset>
        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection