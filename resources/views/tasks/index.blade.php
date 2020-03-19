@extends('layouts.page')

@section('title', 'Global Task List')
@section('page-title', 'Global Task List')
@section('page-lead', 'This page lists the onboarding tasks the choir must complete for every singer. ')

@section('page-content')

	<table class="table table-striped table-bordered bg-light">
		<thead class="">
			<tr>
				<th scope="col">Name</th>
				<th scope="col">Role</th>
				<th scope="col">Type</th>
			</tr>
		</thead>
		<tbody>
			@each('tasks.index_row', $tasks, 'task', 'partials.noresults')
		</tbody>
	</table>

@endsection