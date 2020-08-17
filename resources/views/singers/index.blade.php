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

	@can('viewAny', \App\Models\VoicePart::class)
		<a href="{{route( 'voice-parts.index' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-users-class"></i> Manage Voice Parts</a>
	@endcan
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
		<div class="card-tabs nav nav-tabs">
			<a href="#pane-all" class="card-tab nav-link" id="tab-all" data-toggle="tab">All <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $all_singers->count() }}</span></a>
			<a href="#pane-active" class="card-tab nav-link active" id="tab-active" data-toggle="tab">Active <span class="badge badge-ligh ml-1 d-none d-md-inline-block">{{ $active_singers->count() }}</span></a>
			<a href="#pane-members" class="card-tab nav-link" id="tab-members" data-toggle="tab">Members <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $member_singers->count() }}</span></a>
			<a href="#pane-prospects" class="card-tab nav-link" id="tab-prospects" data-toggle="tab">Prospects <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $prospect_singers->count() }}</span></a>
			<a href="#pane-archived" class="card-tab nav-link" id="tab-archived" data-toggle="tab">Archived <span class="badge badge-light ml-1 d-none d-md-inline-block">{{ $archived_singers->count() }}</span></a>
		</div>

		<div class="tab-content">
			<div class="tab-pane" id="pane-all" role="tabpanel" aria-labelledby="tab-all">
				@include('singers.table', ['singers' => $all_singers, 'col_category' => true, 'col_progress' => false])
			</div>
			<div class="tab-pane active" id="pane-active" role="tabpanel" aria-labelledby="tab-active">
				@include('singers.table', ['singers' => $active_singers, 'col_category' => true, 'col_progress' => false])
			</div>
			<div class="tab-pane" id="pane-members" role="tabpanel" aria-labelledby="tab-members">
				@include('singers.table', ['singers' => $member_singers, 'col_category' => false, 'col_progress' => false])
			</div>
			<div class="tab-pane" id="pane-prospects" role="tabpanel" aria-labelledby="tab-prospects">
				@include('singers.table', ['singers' => $prospect_singers, 'col_category' => false, 'col_progress' => true])
			</div>
			<div class="tab-pane" id="pane-archived" role="tabpanel" aria-labelledby="tab-archived">
				@include('singers.table', ['singers' => $archived_singers, 'col_category' => true, 'col_progress' => false])
			</div>
		</div>

	</div>


@endsection