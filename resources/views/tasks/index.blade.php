@extends('layouts.page')

@section('title', 'Global Task List')
@section('page-title')
<i class="fal fa-tasks fa-fw"></i> Global Task List
@endsection
@section('page-lead', 'This page lists the onboarding tasks the choir must complete for every singer. ')

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h4">Task List</h3>

		<table class="table table-striped table-borderless bg-light">
			<thead class="table-light">
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