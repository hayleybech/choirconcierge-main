@extends('layouts.page')

@section('title', 'Edit Member Profile - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( [ 'route' => ['singers.profiles.update', [$singer, $profile]], 'method' => 'put' ] ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="row">
		<div class="col-md-6">

			<div class="card">
				<h3 class="card-header h4">Edit Member Profile</h3>

				<div class="card-body">
					<div class="form-group">
						<date-input label="Date of Birth" input-name="dob_input" output-name="dob" value="{{ $profile->dob }}"></date-input>
					</div>

					<fieldset>
						<legend>Contact Details</legend>

						<p>
							<x-inputs.text label="Phone" id="phone" name="phone" :value="$profile->phone"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Emergency Contact Name" id="ice_name" name="ice_name" :value="$profile->ice_name"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Emergency Contact Phone" id="ice_phone" name="ice_phone" :value="$profile->ice_phone"></x-inputs.text>
						</p>
					</fieldset>

					<fieldset>
						<legend>Address</legend>
						<p>
							<x-inputs.text label="Street Address" id="address_street_1" name="address_street_1" :value="$profile->address_street_1"></x-inputs.text>
						</p>
						<p>
							<x-inputs.text label="Street Address 2" id="address_street_2" name="address_street_2" :value="$profile->address_street_2"></x-inputs.text>
						</p>
						<div class="row mb-3">
							<div class="col-md-8">
								<x-inputs.text label="Suburb" id="address_suburb" name="address_suburb" :value="$profile->address_suburb"></x-inputs.text>
							</div>
							<div class="col-md-2">
								@php $states = [
									'ACT' => 'ACT',
									'NSW' => 'NSW',
									'NT'  => 'NT',
									'QLD' => 'QLD',
									'SA'  => 'SA',
									'TAS' => 'TAS',
									'VIC' => 'VIC',
									'WA'  => 'WA',
								];@endphp
								<x-inputs.select label="State" id="address_state" name="address_state" :options="$states"></x-inputs.select>
							</div>
							<div class="col-md-2">
								<x-inputs.text label="Postcode" id="address_postcode" name="address_postcode" :value="$profile->address_postcode"></x-inputs.text>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Other Info</legend>

						<p>
							<x-inputs.text label="Why are you joining?" id="reason_for_joining" name="reason_for_joining" :value="$profile->reason_for_joining"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Where did you hear about us?" id="referrer" name="referrer" :value="$profile->referrer"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="What is your profession?" id="profession" name="profession" :value="$profile->profession"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="What non-musical skills do you have?" id="skills" name="skills" :value="$profile->skills"></x-inputs.text>
						</p>
						<div class="form-group">
							{{ Form::label('height', 'Height') }}
							<div class="input-group">
								{{ Form::number('height', $profile->height, ['class' => 'form-control', 'step' => '0.05']) }}
								<div class="input-group-append">
									<div class="input-group-text">cm</div>
								</div>
							</div>
							<small class="form-text text-muted">Knowing the singer's height is useful for riser stacks.</small>
						</div>
						<div class="form-group">
							<x-inputs.text label="Society Membership Details (e.g. BHA #1234)" id="membership_details" name="membership_details" :value="$profile->membership_details"></x-inputs.text>
						</div>
					</fieldset>

				</div>

				<div class="card-footer">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-fw fa-check"></i> Save
					</button>
					<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-link text-danger">
						<i class="fa fa-fw fa-times"></i> Cancel
					</a>
				</div>
			</div>

		</div>
	</div>


{{ Form::close() }}

@endsection