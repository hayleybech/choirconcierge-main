@extends('layouts.page')

@section('title', 'Voice Parts')
@section('page-title')
<i class="fal fa-users-class fa-fw"></i> Voice Parts
@endsection

@section('page-lead', 'On this page you can tweak the names of your voice parts, and add/remove them when necessary. NOTE: Changing an existing part will affect singers in that part.')

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h4">Voice Part List</h3>

		<div class="list-group-flush">
			@foreach($parts as $part)
				<div class="list-group-item d-flex justify-content-between">
					{{ $part->title }}
				</div>
			@endforeach
		</div>
	</div>

@endsection