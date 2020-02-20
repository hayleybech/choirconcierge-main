@extends('layouts.app')

@section('title', 'Singers')

@section('content')

	<h2 class="mb-4">Singers <a href="{{route( 'singer.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-user-plus"></i> Add New</a></h2>

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

	<form method="get" class="form-inline mb-0">
		@foreach( $filters as $filter )
		<div class="input-group input-group-sm mb-2 mr-2">
			<div class="input-group-prepend">
				@php
				$label_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary bg-primary text-white' : 'bg-light';
				@endphp
				<label for="{{ $filter['name']}} " class="input-group-text {{$label_class}}">{{ $filter['label'] }}</label>
			</div>
				@php
				$field_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary' : '';
				echo Form::select($filter['name'],
					$filter['list'],
					$filter['current'],
					['class' => 'custom-select form-control-sm ' . $field_class]
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

	{{--@if ( $categories_keyed[$category] == 'Members')
	<p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
	@endif--}}

	<div class="r-table r-table--card-view-mobile">
		<div class="r-table__thead">
			<div class="r-table__row">
				<div class="r-table__heading column--mark"><input type="checkbox"></div>
				<div class="r-table__heading column--singer"><a href="{{ $sorts['name']['url'] }}">Singer<i class="fa fas sort-{{ $sorts['name']['dir'] }} {{ ($sorts['name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
				<div class="r-table__heading column--progress">Progress</div>
				<div class="r-table__heading column--part"><a href="{{ $sorts['voice_placement.part']['url'] }}">Part<i class="fa fas sort-{{ $sorts['voice_placement.part']['dir'] }} {{ ($sorts['voice_placement.part']['current'] ? 'sort-active' : 'sort-inactive' ) }} "></i></a></div>
				<div class="r-table__heading column--category"><a href="{{ $sorts['category.name']['url'] }}">Category<i class="fa fas sort-{{ $sorts['category.name']['dir'] }} {{ ($sorts['category.name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
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