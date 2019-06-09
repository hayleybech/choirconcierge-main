@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Singers List</h2>
	<p>This page lists all of the singers in the Choir Concierge database. The list shows all forms yet to be completed for each singer. </p>
	
	<p>
		<a href="{{route( 'singer.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-user-plus"></i> Add Singer</a>
	</p>

	@if (session('status'))
	<div class="alert {{ isset($Response->error) || session('fail') ? 'alert-danger' : 'alert-success' }}" role="alert">
		{{ session('status') }}
		
		@isset( $Response->error )
		<pre>
			{{ var_dump($Response) }} 
			@ json($args)
		</pre>
		@endisset
	</div>
	@endif

	<form method="get" class="form-inline mb-4">
		<div class="mr-sm-2">
			Filter
		</div>
		<div class="input-group input-group-sm mr-sm-2 mb-2">
			<div class="input-group-prepend">
				<label for="filter_category" class="input-group-text">Category</label>
			</div>
			@php
			echo Form::select('filter_category', $categories_keyed,
			$category, ['class' => 'custom-select form-control-sm']);
			@endphp
		</div>
		<div class="input-group input-group-sm mr-sm-2 mb-2">
			<div class="input-group-prepend">
				<label for="filter_age" class="input-group-text">Age</label>
			</div>
			<select id="filter_age" name="filter_age" class="form-control-sm custom-select">
				<option value="any">Any</option>
				<option value="under_25">Under 25</option>
				<option value="over_25">Over 25</option>
			</select>
		</div>
		<div class="input-group input-group-sm mr-sm-2 mb-2">
			<div class="input-group-prepend">
				<label for="filter_task" class="input-group-text">Waiting on</label>
			</div>
			<select id="filter_task" name="filter_task" class="form-control-sm custom-select">
				<option>Any</option>
				<option>Member Profile</option>
				<option>Voice Placement</option>
			</select>
		</div>
		<div class="mr-sm-2">
			Sort
		</div>
		<div class="input-group input-group-sm mr-sm-2 mb-2">
			<div class="input-group-prepend">
				<label for="sort_by" class="input-group-text">Sort by</label>
			</div>
			<select id="sort_by" name="sort_by" class="form-control-sm custom-select">
				<option value="name">Name</option>
				<option value="created_at">Date added</option>
			</select>
		</div>
		<input type="submit" value="Apply" class="btn btn-secondary btn-sm">
	</form>

	<h3>{{ $categories_keyed[$category] }}</h3>
	{{--@if ( $categories_keyed[$category] == 'Members')
	<p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
	@endif--}}
	<div class="row">
		@each('partials.singer', $singers, 'singer', 'partials.noresults')
	</div>

@endsection