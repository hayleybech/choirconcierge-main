@extends('layouts.page')

@section('title', 'Add Singer')
@section('page-title', 'Add Singer')

@section('page-content')

{{ Form::open( array( 'route' => 'singers.index' ) ) }}

	<div class="card bg-light">
		<div class="card-header">Singer Details</div>

		<div class="card-body">
			<p>
				{{ Form::label('name', 'Name') }}
				{{ Form::text('name', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('email', 'E-Mail Address') }}
				{{ Form::email('email', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('password', 'Change Password') }}
				{{ Form::password('password', array('class' => 'form-control')) }}
				<small class="form-text text-muted">You may leave this blank and update it later.</small>
			</p>
			<p>
				{{ Form::label('password_confirmation', 'Confirm Password') }}
				{{ Form::password('password_confirmation', array('class' => 'form-control')) }}
			</p>

			<fieldset class="form-group">
				<legend class="col-form-label">Onboarding</legend>

				<div class="custom-control custom-radio">
					<input id="onboarding_enabled_yes" name="onboarding_enabled" value="1" class="custom-control-input" type="radio" checked>
					<label for="onboarding_enabled_yes" class="custom-control-label">
						Enabled
						<small class="text-muted ml-2">
							Choose this option for new/prospective singers.
						</small>
					</label>
				</div>

				<div class="custom-control custom-radio">
					<input id="onboarding_enabled_no" name="onboarding_enabled" value="0" class="custom-control-input" type="radio">
					<label for="onboarding_enabled_no" class="custom-control-label">
						Disabled
						<small class="text-muted ml-2">
							Choose this option when you're adding existing singers.
						</small>
					</label>
				</div>
			</fieldset>

			<div class="form-group">
				<label for="user_roles"><i class="fa fa-fw fa-users"></i> Roles</label><br>
				<select id="user_roles" name="user_roles[]" class="select2 form-control" data-model="roles" multiple></select>
			</div>

		</div>

		<div class="card-footer">
			{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
		</div>
	</div>

{{ Form::close() }}

@endsection