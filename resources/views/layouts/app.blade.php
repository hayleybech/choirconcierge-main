<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Choir Concierge') }} - @yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">

				<a class="navbar-brand" href="{{ url('/') }}">
					<img src="/img/logo.png" alt="Choir Concierge" class="logo">
				</a>

				<!-- Left Side Of Navbar -->
				<ul class="navbar-nav">

				</ul>

				<!-- Right Side Of Navbar -->
				<ul class="navbar-nav">
					<li class="nav-item dropdown">
						<a href="#" id="notificationsDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
							<i class="fa fa-bell"></i> Notifications <span class="badge badge-pill badge-secondary">0</span>
						</a>

						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationsDropdown">
							<h6 class="dropdown-header">Singer Completed Member Profile</h6>
							<p class="dropdown-item text-muted">Hi Hayden, A member profile has been completed for John Citizen. </p>
						</div>
					</li>
					<li class="nav-item">
						<span id="changelog-link" class="navbar-text">
							<i class="fa fa-code"></i> <span class="headway-badge"></span>
						</span>
					</li>
					<!-- Authentication Links -->
					@guest
						<li class="nav-item {{ ( \Request::is('login') ) ? 'active' : '' }}">
							<a href="{{ route('login') }}" class="nav-link"><i class="fa fa-sign-in fa-fw"></i> Login</a>
						</li>
						<li class="nav-item {{ ( \Request::is('register') ) ? 'active' : '' }}">
							<a href="{{ route('register') }}" class="nav-link"><i class="fa fa-user-plus fa-fw"></i> Register</a>
						</li>
					@else
						<li class="nav-item dropdown">
							<a href="#" id="navbarDropdown" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
								<i class="fa fa-user-circle fa-fw"></i> {{ Auth::user()->name }} <span class="caret"></span>
							</a>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
								
								<a class="dropdown-item {{ ( \Request::is('dash') ) ? 'active' : '' }}" href="{{ route('dash') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
								<div class="dropdown-divider"></div>
								
								@if( Auth::user()->hasRole('Admin') )
								<a href="{{ route('users.index') }}" class="dropdown-item"><i class="fa fa-group fa-fw"></i> Users List</a>
								<a href="{{ route('tasks.index') }}" class="dropdown-item"><i class="fa fa-list-alt fa-fw"></i> Tasks List</a>
								@endif
								
								@if( Auth::user()->hasRole('Membership Team') )
								<a href="{{ route('memberprofile.new') }}" class="dropdown-item" target="_blank"><i class="fa fa-plus fa-fw"></i> Member Profile</a>
								@endif
								@if( Auth::user()->hasRole('Music Team') )
								<a href="{{ route('voiceplacement.new') }}" class="dropdown-item" target="_blank"><i class="fa fa-plus fa-fw"></i> Voice Placement</a>	
								@endif
								@if( Auth::user()->isEmployee() )
								<a href="{{ route('singers.index') }}" class="dropdown-item {{ ( \Request::is('singers.index') ) ? 'active' : '' }}"><i class="fa fa-list-alt fa-fw"></i> Singers List</a>
								@endif
								
								
								<div class="dropdown-divider"></div>
								
								<a href="{{ route('logout') }}" 
									class="dropdown-item {{ ( \Request::is('logout') ) ? 'active' : '' }}"
									onclick="event.preventDefault();
											 document.getElementById('logout-form').submit();">
									<i class="fa fa-sign-out fa-fw"></i> Logout
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
								
							</div>
						</li>
					@endguest
				</ul>
                
            </div>
        </nav>
		

		<main>
			<div class="container">
			@yield('content')
			</div>
		</main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <script src="https://use.fontawesome.com/7679517f07.js"></script>
	
	<script>
	  var HW_config = {
		selector: ".headway-badge", // CSS selector where to inject the badge
		account:  "7L6Rky"
	  }
	</script>
	<script async src="//cdn.headwayapp.co/widget.js"></script>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
	
</body>
</html>
