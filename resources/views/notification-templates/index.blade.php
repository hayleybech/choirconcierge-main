@extends('layouts.page')

@section('title', 'Notification Templates')
@section('page-title')
<i class="fal fa-fw fa-clone"></i> Notification Templates
@endsection
@section('page-lead', 'This page lists notifications sent whenever a task is completed. ')

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h4">Templates List</h3>

		<div class="table-responsive">
			<table class="table table-borderless table-striped bg-light">
				<thead>
				<tr>
					<th scope="col">Task</th>
					<th scope="col">Subject</th>
					<th scope="col">Recipients</th>
					<th scope="col">Body</th>
					<th scope="col">Delay</th>
				</tr>
				</thead>
				<tbody>
				@each('notification-templates.index_row', $templates, 'template', 'partials.noresults')
				</tbody>
			</table>
		</div>
	</div>


@endsection