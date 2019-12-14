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
		<div class="input-group input-group-sm">
			<div class="input-group-prepend">
				<label for="filter_category" class="input-group-text">Category</label>
			</div>
			@php
			echo Form::select('filter_category', $categories_keyed,
			$category, ['class' => 'custom-select form-control-sm']);
			@endphp
			
			<div class="input-group-append">
				<input type="submit" value="Filter" class="btn btn-secondary btn-sm">
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

	<div class="r-table r-table--card-view-mobile">
		<div class="r-table__row">
			<div class="r-table__heading column--mark"><input type="checkbox"></div>
			<div class="r-table__heading column--singer">Singer</div>
			<div class="r-table__heading column--progress">Progress</div>
			<div class="r-table__heading column--part">Part</div>
			<div class="r-table__heading column--category">Category</div>
			<div class="r-table__heading column--phone">Phone</div>
			<div class="r-table__heading column--age">Age</div>
			<div class="r-table__heading column--actions">Actions</div>
		</div>
		<div class="r-table__tbody">
			@each('partials.singerrow', $singers, 'singer', 'partials.noresults')
		</div>
	</div>

@endsection