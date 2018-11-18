@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Global Task List</h2>
	<p>This page lists the onboarding tasks the choir must complete for every singer. </p>

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
				<th scope="col">Name</th>
				<th scope="col">Role</th>
				<th scope="col">Type</th>
			</tr>
		</thead>
		<tbody>
			@each('partials.task', $tasks, 'task', 'partials.noresults')
		</tbody>
	</table>

@endsection