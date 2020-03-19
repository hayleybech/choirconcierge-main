@extends('layouts.app')

@section('title', 'Global Task List')

@section('content')

	<div class="jumbotron bg-light">
		<h2 class="display-4">Global Task List</h2>
		<p class="lead">This page lists the onboarding tasks the choir must complete for every singer. </p>
	</div>

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