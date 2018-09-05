@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Global Task List</h2>
	<p>This page lists the onboarding tasks the choir must complete for every singer. </p>
	
	<p>
		<a href="{{route( 'singer.create' )}}" class="btn btn-add btn-sm btn-success"><i class="fa fa-fw fa-plus"></i>Add Singer</a>
	</p>

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
		
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Role</th>
				<th>Type</th>
			</tr>
		</thead>
		<tbody>
			@each('partials.task', $tasks, 'task', 'partials.noresults')
		</tbody>
	</table>

@endsection