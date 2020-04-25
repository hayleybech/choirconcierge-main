@extends('layouts.page')

@section('title', 'Singers')
@section('page-title')
<i class="fal fa-fw fa-users"></i> Singers
@endsection
@section('page-action')
	@if(Auth::user()->hasRole('Membership Team'))
		<a href="{{route( 'singer.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-user-plus"></i> Add New</a>
	@endif
@endsection

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h4">Singers List</h3>

		<div class="card-body">

			<?php
			use App\Models\Singer;
			$filters_class = Singer::hasActiveFilters() ? 'btn-primary' : 'btn-outline-secondary';
			?>
			<a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>

			<div class="collapse mt-2" id="filters">

				<form method="get" class="form-inline mb-0">
					@each('partials.filter', $filters, 'filter')

					<div class="input-group input-group-sm mb-2 mr-2">
						<div class="btn-group" role="group" aria-label="Basic example">
							<button class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> Apply</button>
							<a href="{{ route('singers.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
						</div>
					</div>
				</form>

			</div>


			{{--@if ( $categories_keyed[$category] == 'Members')
            <p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
            @endif--}}

		</div>

		<div class="r-table r-table--card-view-mobile">
			<div class="r-table__thead">
				<div class="r-table__row row--singer">
					<div class="r-table__heading col--mark"><input type="checkbox"></div>
					<div class="r-table__heading col--title"><a href="{{ $sorts['name']['url'] }}">Singer<i class="fa fas sort-{{ $sorts['name']['dir'] }} {{ ($sorts['name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
					<div class="r-table__heading singer-col--part"><a href="{{ $sorts['voice_part']['url'] }}">Part<i class="fa fas sort-{{ $sorts['voice_part']['dir'] }} {{ ($sorts['voice_part']['current'] ? 'sort-active' : 'sort-inactive' ) }} "></i></a></div>
					<div class="r-table__heading singer-col--category"><a href="{{ $sorts['category.name']['url'] }}">Category<i class="fa fas sort-{{ $sorts['category.name']['dir'] }} {{ ($sorts['category.name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
					<div class="r-table__heading singer-col--progress">Progress</div>
					<div class="r-table__heading singer-col--actions">Actions</div>
					<div class="r-table__heading col--delete"></div>
				</div>
			</div>
			<div class="r-table__tbody">
				@each('singers.index_row', $singers, 'singer', 'partials.noresults')
			</div>
		</div>

		<div class="card-footer">
			{{ $singers->count() }} singers
		</div>

	</div>


@endsection