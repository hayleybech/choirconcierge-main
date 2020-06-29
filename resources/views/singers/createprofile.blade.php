@extends('layouts.page')

@section('title', 'Add Member Profile - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( array( 'route' => array('profile', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="card bg-light">
		<h3 class="card-header h4">Add Member Profile</h3>

		<div class="card-body">
			<div class="form-group">

				{{ Form::label('dob', 'Date of Birth') }}
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
					</div>
					{{ Form::text('dob_input', '', array('class' => 'form-control dob-single-date-picker')) }}
					{{ Form::hidden('dob', '', array('class' => 'dob-hidden')) }}
				</div>

			</div>

			<p>
				{{ Form::label('phone', 'Phone') }}
				{{ Form::text('phone', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('ice_name', 'Emergency Contact Name') }}
				{{ Form::text('ice_name', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('ice_phone', 'Emergency Contact Phone') }}
				{{ Form::text('ice_phone', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('reason_for_joining', 'Why are you joining?') }}
				{{ Form::text('reason_for_joining', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('referrer', 'Where did you hear about us?') }}
				{{ Form::text('referrer', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('profession', 'What is your profession?') }}
				{{ Form::text('profession', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('skills', 'What non-musical skills do you have?') }}
				{{ Form::text('skills', '', array('class' => 'form-control')) }}
			</p>

		</div>

		<div class="card-footer">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-fw fa-check"></i> Create
			</button>
			<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-outline-secondary">
				<i class="fa fa-fw fa-times"></i> Cancel
			</a>
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