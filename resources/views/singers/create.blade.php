@extends('layouts.page')

@section('title', 'Add Singer')
@section('page-title', 'Add Singer')

@section('page-content')

{{ Form::open( [ 'route' => 'singers.index' ] ) }}

<div class="row">
	<div class="col-md-6">

		<div class="card">
			<div class="card-header"><h3 class="h4">Singer Details</h3></div>

			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<x-inputs.text label="First Name" id="first_name" name="first_name"></x-inputs.text>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<x-inputs.text label="Last Name" id="last_name" name="last_name"></x-inputs.text>
						</div>
					</div>
				</div>

				<p>
					<x-inputs.text label="Email Address" id="email" name="email" type="email"></x-inputs.text>
				</p>

				<p>
					<x-inputs.text label="Password" id="password" name="password" type="password" help-text="You may leave this blank and update it later."></x-inputs.text>
				</p>
				<p>
					<x-inputs.text label="Confirm Password" id="password_confirmation" name="password_confirmation" type="password"></x-inputs.text>
				</p>

				<div class="form-group">
					<x-inputs.select label="Voice Part" id="voice_part_id" name="voice_part_id" :options="$voice_parts"></x-inputs.select>
				</div>

				<toggleable-input label="I'm adding a current member" name="onboarding_disabled" help-text="Onboarding will be disabled when adding an existing singer.">
					<fieldset id="existing_singer_details" style="padding: 15px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; margin-bottom: 10px;">

						<div class="form-group">
							<date-input label="Joined" input-name="joined_at_input" output-name="joined_at"></date-input>
						</div>

						<div class="form-group">
							<label for="user_roles" class="label-optional">Roles</label><br>
							<div class="row">
								@foreach($roles as $role)
									@if($role->name === 'User')
										<input type="hidden" name="user_roles[]" id="user_roles_{{ $role->id }}" value="{{ $role->id }}">
										@continue
									@endif
									<div class="col-md-6">
										<x-inputs.checkbox :label="$role->name" :id="'user_roles_'.$role->id" name="user_roles[]" :value="$role->id"></x-inputs.checkbox>
									</div>
								@endforeach
							</div>
						</div>
					</fieldset>
				</toggleable-input>

			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-fw fa-check"></i> Create
				</button>
				<a href="{{ route('singers.index') }}" class="btn btn-link text-danger">
					<i class="fa fa-fw fa-times"></i> Cancel
				</a>
			</div>
		</div>

	</div>
</div>

{{ Form::close() }}

@endsection