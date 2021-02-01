@extends('layouts.page')

@section('title', 'Onboarding Checklist')
@section('page-title')
	<i class="fal fa-tasks fa-fw"></i> Onboarding Checklist
	@can('create', \App\Models\Task::class)
		<a href="{{route( 'tasks.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-plus"></i> Add Task</a>
	@endcan
@endsection

@section('page-action')
@endsection

@section('page-lead', 'On this page you can manage the onboarding workflow every new singer completes when joining the choir. ')

@section('page-content')

	<div class="card">
		<h3 class="card-header h4">Checklist</h3>

		<table class="table table-striped table-borderless">
			<thead>
			<tr>
				<th scope="col">Name</th>
				<th scope="col">Role</th>
				<th scope="col">Type</th>
				<th scope="col"></th>
			</tr>
			</thead>
			<tbody>
			@each('tasks.index_row', $tasks, 'task', 'partials.noresults')
			</tbody>
		</table>
	</div>

@endsection