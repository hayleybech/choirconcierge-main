@extends('layouts.page')

@section('title', 'Edit Profile - ' . $user->name)
@section('page-title', 'Edit Profile')

@section('page-content')

	{{ Form::open( [ 'route' => ['accounts.update'], 'method' => 'put' ] ) }}

	<div class="row">
		<div class="col-md-6">

			<div class="card">
				<div class="card-header"><h3 class="h4">User Details</h3></div>

				<div class="card-body">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<x-inputs.text label="First Name" id="first_name" name="first_name" :value="old('first_name', $user->first_name)" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<x-inputs.text label="Last Name" id="last_name" name="last_name" :value="old('last_name', $user->last_name)" />
							</div>
						</div>
					</div>

					<div class="form-group">
						<x-inputs.file label="Profile Picture" id="avatar" name="avatar" />
					</div>

					<p>
						<x-inputs.text label="Email Address" id="email" name="email" type="email" :value="old('email', $user->email)" />
					</p>

					<p>
						<x-inputs.text label="Change Password" id="password" name="password" type="password" />
					</p>
					<p>
						<x-inputs.text label="Confirm Password" id="password_confirmation" name="password_confirmation" type="password" />
					</p>

				</div>
			</div>

			<div class="card">
				<div class="card-header"><h3 class="h4">Profile Details</h3></div>

				<div class="card-body">
					<div class="form-group">
						<single-date-input label="Date of Birth" input-name="dob_input" output-name="dob" init-value="{{ old('dob', $user->dob) }}" />
					</div>

					<p>
						<x-inputs.text label="What is your profession?" id="profession" name="profession" :value="old('profession', $user->profession)" />
					</p>

					<p>
						<x-inputs.text label="What non-musical skills do you have?" id="skills" name="skills" :value="old('skills', $user->skills)" />
					</p>
					<div class="form-group">
						{{ Form::label('height', 'Height') }}
						<div class="input-group">
							{{ Form::number('height', old('height', $user->height), ['class' => 'form-control', 'step' => '0.05']) }}
							<div class="input-group-append">
								<div class="input-group-text">cm</div>
							</div>
						</div>
						<small class="form-text text-muted">Knowing the singer's height is useful for riser stacks.</small>
					</div>
					<div class="form-group">
						<x-inputs.text label="BHA Member ID (e.g. 1234)" id="bha_id" name="bha_id" :value="old('bha_id', $user->bha_id)" />
					</div>

				</div>

			</div>

			<div class="card">
				<div class="card-header"><h3 class="h4">Contact Details</h3></div>

				<div class="card-body">

					<p>
						<x-inputs.text label="Phone" id="phone" name="phone" :value="old('phone', $user->phone)" />
					</p>

					<p>
						<x-inputs.text label="Emergency Contact Name" id="ice_name" name="ice_name" :value="old('ice_name', $user->ice_name)" />
					</p>

					<p>
						<x-inputs.text label="Emergency Contact Phone" id="ice_phone" name="ice_phone" :value="old('ice_phone', $user->ice_phone)" />
					</p>

				</div>
			</div>

			<div class="card">
				<div class="card-header"><h3 class="h4">Address</h3></div>

				<div class="card-body">

					<p>
						<x-inputs.text label="Street Address" id="address_street_1" name="address_street_1" :value="old('address_street_1', $user->address_street_1)" />
					</p>
					<p>
						<x-inputs.text label="Street Address 2" id="address_street_2" name="address_street_2" :value="old('address_street_2', $user->address_street_2)" />
					</p>
					<div class="row mb-3">
						<div class="col-md-8">
							<x-inputs.text label="Suburb" id="address_suburb" name="address_suburb" :value="old('address_suburb', $user->address_suburb)" />
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
							<x-inputs.select label="State" id="address_state" name="address_state" :options="$states" selected="{{ old('address_state', $user->address_state) }}" />
						</div>
						<div class="col-md-2">
							<x-inputs.text label="Postcode" id="address_postcode" name="address_postcode" :value="old('address_postcode', $user->address_postcode)" />
						</div>
					</div>

				</div>

				<div class="card-footer">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-fw fa-check"></i> Save
					</button>
				</div>
			</div>

		</div>
	</div>


{{ Form::close() }}

@endsection