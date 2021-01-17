@extends('layouts.public')

@section('title', 'Welcome')
@section('page-title', 'Welcome')

@section('public-content')
	<div class="container-fluid">
		<div class="cc-header">
			<div>
				<h1 class="h1">DESIGNED TO MAKE CHOIR MANAGEMENT EASY!</h1>
				<p class="lead">Choir Concierge is a new choir management software designed to be a powerful user-friendly tool for all your day-to-day choir management needs.</p>
					<p>bkawbgd;</p>
				  <p>Log in using the link below. </p>
				  <p class="lead">
					<a class="btn btn-primary btn-lg" href="{{ route('dash') }}" role="button">Dashboard</a>
				  </p>

			</div>
			<div>
				<img src="{{ global_asset('/img/group-58.svg') }}">
			</div>
		</div>
	</div>
@endsection
