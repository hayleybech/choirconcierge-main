@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Singers List</h2>
	<p>This page lists all of the singers in the Choir Concierge database. The list shows all forms yet to be completed for each singer. </p>

	@if (session('status'))
	<div class="alert {{ isset($Response->error) ? 'alert-danger' : 'alert-success' }}" role="alert">
		{{ session('status') }}
		
		@isset( $Response->error )
		<pre>
			{{ var_dump($Response) }} 
			@json($args)
		</pre>
		@endisset
	</div>
	@endif
		
	<h3>Prospective Members</h2>
	<div class="row">
		@each('partials.singer', $prospects, 'singer', 'partials.noresults')
	</div>
	
	<h3>Members</h2>
	<p><a href="{{ route('singers.export') }}" class="btn btn-link btn-sm">Export paid Singers.</a></p>
	<div class="row">
		@each('partials.singer', $members, 'singer', 'partials.noresults')
	</div>

@endsection