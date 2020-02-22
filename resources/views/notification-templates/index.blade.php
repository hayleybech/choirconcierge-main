@extends('layouts.app')

@section('title', 'Notification Templates')

@section('content')

	<h2 class="display-4 mb-4">Notification Templates</h2>
	<p>This page lists notifications sent whenever a task is completed. </p>

	@if (session('status'))
	<div class="alert {{ isset($Response->error) ? 'alert-danger' : 'alert-success' }}" role="alert">
		{{ session('status') }}
		
		@isset( $Response->error )
		<pre>
			{{ var_dump($Response) }} 
			@ json($args)
		</pre>
		@endisset
	</div>
	@endif
		
	<table class="table table-striped table-bordered">
		<thead class="thead-dark">
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

@endsection