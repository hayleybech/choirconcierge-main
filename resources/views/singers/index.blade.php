@extends('layouts.page')

@section('title', 'Singers')
@section('page-title')
<i class="fal fa-fw fa-users"></i> Singers
	@can('create', \App\Models\Singer::class)
		<a href="{{route( 'singers.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-user-plus"></i> Add New</a>
	@endcan
@endsection
@section('page-action')
	<?php
	use App\Models\Singer;
	$filters_class = Singer::hasActiveFilters() ? 'btn-primary' : 'btn-light';
	?>
	<a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>

	@if(Auth::user()->hasRole('Music Team'))
		<a href="{{route( 'voice-parts.index' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-users-class"></i> Manage Voice Parts</a>
	@endif
@endsection

@section('page-content')

	<div class="d-flex justify-content-end">

		<div class="collapse mt-2" id="filters">

			<form method="get" class="form-inline mb-0">
				@each('partials.filter', $filters, 'filter')

				<div class="input-group input-group-sm mb-2 mr-2">
					<div class="btn-group" role="group" aria-label="Basic example">
						<button class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> Apply</button>
						<a href="{{ route('singers.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
					</div>
				</div>
			</form>

		</div>
	</div>

	<div class="card">

		<div class="card-header justify-content-between align-items-center">
			
		</div>

		<table class="table card-table">
			<thead>
				<tr class="row--singer">
					<th class="col--title"><a href="{{ $sorts['name']['url'] }}">Singer<i class="fa fas sort-{{ $sorts['name']['dir'] }} {{ ($sorts['name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
					<th class="col--part"><a href="{{ $sorts['voice_part']['url'] }}">Part<i class="fa fas sort-{{ $sorts['voice_part']['dir'] }} {{ ($sorts['voice_part']['current'] ? 'sort-active' : 'sort-inactive' ) }} "></i></a></th>
					<th class="col--category"><a href="{{ $sorts['category.name']['url'] }}"><span class="status__title">Category</span><i class="fa fas sort-{{ $sorts['category.name']['dir'] }} {{ ($sorts['category.name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
					<th class="col--progress">Progress</th>
					<th class="col--actions">Actions</th>
					<th class="col--delete"></th>
				</tr>
			</thead>
			<tbody>
				@each('singers.index_row', $singers, 'singer', 'partials.noresults')
			</tbody>
		</table>

		<div class="card-footer">
			{{ $singers->count() }} singers
		</div>

	</div>


@endsection