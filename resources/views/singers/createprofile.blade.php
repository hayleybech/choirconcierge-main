@extends('layouts.page')

@section('title', 'Add Member Profile - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( [ 'route' => ['singers.profiles.store', $singer->id] ] ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="row">
		<div class="col-md-6">


			<div class="card">
				<h3 class="card-header h4">Add Member Profile</h3>

				<div class="card-body">
					<div class="form-group">
						<date-input label="Date of Birth" input-name="dob_input" output-name="dob"></date-input>
					</div>

					<fieldset>
						<legend>Contact Details</legend>

						<p>
							<x-inputs.text label="Phone" id="phone" name="phone"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Emergency Contact Name" id="ice_name" name="ice_name"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Emergency Contact Phone" id="ice_phone" name="ice_phone"></x-inputs.text>
						</p>
					</fieldset>

					<fieldset>
						<legend>Address</legend>

						<p>
							<x-inputs.text label="Street Address" id="address_street_1" name="address_street_1"></x-inputs.text>
						</p>
						<p>
							<x-inputs.text label="Street Address 2" id="address_street_2" name="address_street_2"></x-inputs.text>
						</p>
						<div class="row mb-3">
							<div class="col-md-8">
								<x-inputs.text label="Suburb" id="address_suburb" name="address_suburb"></x-inputs.text>
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
								<x-inputs.text label="Postcode" id="address_postcode" name="address_postcode"></x-inputs.text>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>Other Info</legend>

						<p>
							<x-inputs.text label="Why are you joining?" id="reason_for_joining" name="reason_for_joining"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="Where did you hear about us?" id="referrer" name="referrer"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="What is your profession?" id="profession" name="profession"></x-inputs.text>
						</p>

						<p>
							<x-inputs.text label="What non-musical skills do you have?" id="skills" name="skills"></x-inputs.text>
						</p>
						<div class="form-group">
							{{ Form::label('height', 'Height') }}
							<div class="input-group">
								{{ Form::number('height', '', ['class' => 'form-control', 'step' => '0.05']) }}
								<div class="input-group-append">
									<div class="input-group-text">cm</div>
								</div>
							</div>
							<small class="form-text text-muted">Knowing the singer's height is useful for riser stacks.</small>
						</div>
						<div class="form-group">
							<x-inputs.text label="Society Membership Details (e.g. BHA #1234)" id="membership_details" name="membership_details"></x-inputs.text>
						</div>
					</fieldset>

				</div>

				<div class="card-footer">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-fw fa-check"></i> Create
					</button>
					<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-link text-danger">
						<i class="fa fa-fw fa-times"></i> Cancel
					</a>
				</div>
			</div>

		</div>
	</div>


{{ Form::close() }}

@push('scripts-footer-bottom')
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

	<script>
		// DATREPICKER CONFIG
		const DATE_FORMAT_RAW = 'YYYY-MM-DD HH:mm:ss';
		const DATE_FORMAT_DISPLAY = 'MMMM D, YYYY';

		const DATE_CONFIG = {
			"showDropdowns": true,
			"showISOWeekNumbers": true,
			"timePicker": false,
			"locale": {
				"format": DATE_FORMAT_DISPLAY,
				"firstDay": 1
			}
		};

		// DOB (Single Date Picker)
		const $el_dob = $('.dob-single-date-picker');
		const $el_dob_raw = $('.dob-hidden');
		$el_dob.daterangepicker({
					...DATE_CONFIG,
					'singleDatePicker': true
				},
				function(start, end, label){
					console.log(start.format(DATE_FORMAT_RAW));
					$el_dob_raw.val( start.format(DATE_FORMAT_RAW) );
				}
		);
	</script>
@endpush

@endsection