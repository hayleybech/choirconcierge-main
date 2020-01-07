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
		@foreach( $filters as $filter )
		<div class="input-group input-group-sm mb-2 mr-2">
			<div class="input-group-prepend">
				<label for="{{ $filter['name']}} " class="input-group-text">{{ $filter['label'] }}</label>
			</div>
				@php
				$class = ( $filter['current'] != $filter['default'] ) ? 'border-primary' : '';
				echo Form::select($filter['name'],
					$filter['list'],
					$filter['current'],
					['class' => 'custom-select form-control-sm ' . $class]
				);
				@endphp
		</div>
		@endforeach

		<div class="input-group input-group-sm mb-2 mr-2">
			<div class="btn-group" role="group" aria-label="Basic example">
				<button class="btn btn-outline-secondary btn-sm"><i class="fa fa-filter"></i> Filter</button>
				<a href="{{ route('singers.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
			</div>
		</div>
	</form>

	<h3>{{ $filters['cat']['list'][$filters['cat']['current']] }}</h3>
	{{--@if ( $categories_keyed[$category] == 'Members')
	<p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
	@endif--}}

	<div class="r-table r-table--card-view-mobile">
		<div class="r-table__thead">
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
		</div>
		<div class="r-table__tbody">
			@each('partials.singerrow', $singers, 'singer', 'partials.noresults')
		</div>
	</div>

@endsection