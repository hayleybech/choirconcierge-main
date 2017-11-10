@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2>Singers List</h2>
	<p>This page lists all of the prospective singers in the Choir Concierge database. The list shows all forms yet to be completed for each singer. </p>

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
		
	<div class="row">

		@each('partials.singer', $singers, 'singer')

	</div>

@endsection