@extends('layouts.public')

@section('title', 'Welcome')
@section('page-title', 'Welcome')

@section('public-content')
	<div class="jumbotron card">
	  <h1 class="display-3">Welcome</h1>
	  <p class="lead">Welcome to Choir Concierge, your singer management assistant. </p>
	  <hr class="my-4">
	  <p>Log in using the link below. </p>
	  <p class="lead">
		<a class="btn btn-primary btn-lg" href="{{ route('dash') }}" role="button">Dashboard</a>
	  </p>
	</div>
@endsection
