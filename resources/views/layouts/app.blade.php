<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Choir Concierge') }}</title>

    <!-- Styles -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
		<header id="app-header">

			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				<a class="" href="{{ url('/') }}">
					<img src="/img/logo.png" alt="Choir Concierge" class="logo">
					<img src="/favicon.png" alt="Choir Concierge" class="logo-collapse">
				</a>


				<ul class="navbar-nav nav-vertical">
					<!-- Authentication Links -->
					@guest
					<li class="nav-item {{ ( \Request::is('login') ) ? 'active' : '' }}">
						<a href="{{ route('login') }}" class="nav-link"><i class="fa fa-sign-in fa-fw"></i> Login</a>
					</li>
					<li class="nav-item {{ ( \Request::is('register') ) ? 'active' : '' }}">
						<a href="{{ route('register') }}" class="nav-link"><i class="fa fa-user-plus fa-fw"></i> Register</a>
					</li>
					@else

					<li class="nav-item">
						<a class="nav-link {{ ( \Request::is('dash') ) ? 'active' : '' }}" href="{{ route('dash') }}"><i class="fa fa-tachometer-alt fa-fw"></i><span class="link-text"> Dashboard</span></a>
					</li>

					{{--<li class="nav-item dropdown">
						<a href="#" id="notificationsDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
							<i class="fa fa-bell"></i> Notifications <span class="badge badge-pill badge-secondary">{{$notifications->count()}}</span>
						</a>

						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationsDropdown">
							@foreach( $notifications as $notification )
								<span class="dropdown-item text-muted"><strong>{{ $notification->data['subject'] }}</strong><br> {{ $notification->data['body'] }}</span>
							@endforeach
						</div>
					</li>--}}

					@if( Auth::user()->isEmployee() )
					<li class="nav-item nav-separator"></li>
					<li class="nav-item">
						<a href="{{ route('singers.index') }}" class="nav-link {{ ( \Request::is('singers.index') ) ? 'active' : '' }}"><i class="fa fa-users fa-fw"></i><span class="link-text"> Singers</span></a>
					</li>
					<li class="nav-item">
						<a href="{{ route('songs.index') }}" class="nav-link {{ ( \Request::is('songs.index') ) ? 'active' : '' }}"><i class="fa fa-music fa-fw"></i><span class="link-text"> Songs</span></a>
					</li>
					@endif

					@if( Auth::user()->hasRole('Admin') )
					<li class="nav-item nav-separator"></li>
					<li class="nav-item">
						<a href="{{ route('tasks.index') }}" class="nav-link"><i class="fa fa-tasks fa-fw"></i><span class="link-text"> Tasks</span></a>
					</li>
					<li class="nav-item">
						<a href="{{ route('notification-templates.index') }}" class="nav-link"><i class="fa fa-clone fa-fw"></i><span class="link-text"> Templates</span></a>
					</li>
					<li class="nav-item">
						<a href="{{ route('users.index') }}" class="nav-link"><i class="fa fa-sitemap fa-fw"></i><span class="link-text"> Team</span></a>
					</li>
					@endif

					<li class="nav-item nav-separator"></li>

					<li class="nav-item">
						<a href="#" class="nav-link">
							<i class="fa fa-user-circle fa-fw"></i><span class="link-text"> {{ Auth::user()->name }}</span>
						</a>
					</li>

					<li class="nav-item">
						<a href="https://headwayapp.co/choir-concierge-updates?utm_medium=widget" target="_blank" id="changelog-link" class="nav-link"><i class="fa fa-fw fa-code"></i> <span class="link-text">Updates </span><span class="headway-badge"></span></a>
					</li>

					<li class="nav-item">
						<a href="{{ route('logout') }}"
						   class="nav-link {{ ( \Request::is('logout') ) ? 'active' : '' }}"
						   onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
							<i class="fa fa-sign-out-alt fa-fw"></i> <span class="link-text">Logout</span>
						</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							{{ csrf_field() }}
						</form>
					</li>

					@endguest

					<li class="nav-item">
						<a href="" class="nav-link nav-collapse-link"><i class="fa fa-fw fa-caret-square-left"></i><span class="link-text"> Collapse Menu</span></a>
					</li>
				</ul>

			</nav>
		</header>


		<main>
			<div class="container">
			@yield('content')
			</div>
		</main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <!--<script src="https://use.fontawesome.com/7679517f07.js"></script>-->
	<script src="https://kit.fontawesome.com/baff915cc9.js" crossorigin="anonymous"></script>
	
	<script>
	  var HW_config = {
		selector: ".headway-badge", // CSS selector where to inject the badge
		account:  "7L6Rky"
	  }
	</script>
	<script async src="//cdn.headwayapp.co/widget.js"></script>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

	@stack('scripts-footer-bottom')
	
</body>
</html>
