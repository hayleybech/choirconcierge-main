@extends('layouts.page')

@section('title', 'Welcome')
@section('page-title', 'Welcome')

@section('page-content')
	<div class="jumbotron card bg-light">
	  <h1 class="display-3">Welcome</h1>
	  <p class="lead">Welcome to Choir Concierge, your singer management assistant. </p>
	  <hr class="my-4">
	  <p>Log in using the link below. </p>
	  <p class="lead">
		<a class="btn btn-primary btn-lg" href="{{ route('dash') }}" role="button">Dashboard</a>
	  </p>
	</div>
@endsection
