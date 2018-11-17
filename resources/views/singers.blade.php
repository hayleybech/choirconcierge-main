@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Singers List</h2>
	<p>This page lists all of the singers in the Choir Concierge database. The list shows all forms yet to be completed for each singer. </p>
	
	<p>
		<a href="{{route( 'singer.create' )}}" class="btn btn-add btn-sm btn-success"><i class="fa fa-fw fa-plus"></i>Add Singer</a>
	</p>

	@if (session('status'))
	<div class="alert {{ isset($Response->error) ? 'alert-danger' : 'alert-success' }}" role="alert">
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
		<div class="input-group input-group-sm">
			<div class="input-group-prepend">
				<label for="filter_category" class="input-group-text">Category</label>
			</div>
			@php
			echo Form::select('filter_category', $categories_keyed,
			$category, ['class' => 'custom-select form-control-sm']);
			@endphp
			
			<div class="input-group-append">
				<input type="submit" value="Filter" class="btn btn-primary btn-sm">
			</div>
		</div>
	</form>

	<h3>{{ $categories_keyed[$category] }}</h3>
	{{--@if ( $categories_keyed[$category] == 'Members')
	<p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
	@endif--}}
	<div class="row">
		@each('partials.singer', $singers, 'singer', 'partials.noresults')
	</div>

@endsection