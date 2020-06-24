<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Choir Concierge') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link rel="shortcut icon" href="/img/favicon.png">

	<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

</head>
<body>
    <div id="app">

		<div id="app-top">

			<header id="app-header">

				<nav class="navbar navbar-expand-lg navbar-light bg-light">
					<a class="" href="{{ url('/') }}">
						<img src="/img/choir-logo.png" alt="Choir Concierge" class="logo">
						<img src="/favicon.png" alt="Choir Concierge" class="logo-collapse">
					</a>


					<ul class="navbar-nav nav-vertical">
						<!-- Authentication Links -->
						@guest
							<li class="nav-item">
								<a href="{{ route('login') }}" class="nav-link {{ ( request()->routeIs('login') ) ? 'active' : '' }}"><i class="fad fa-sign-in-alt fa-fw"></i> Login</a>
							</li>
							{{--
							<li class="nav-item {{ ( request()->routeIs('register') ) ? 'active' : '' }}">
								<a href="{{ route('register') }}" class="nav-link"><i class="fa fa-user-plus fa-fw"></i> Register</a>
							</li>--}}
						@else

							<li class="nav-item">
								<a class="nav-link {{ ( request()->routeIs('dash') ) ? 'active' : '' }}" href="{{ route('dash') }}"><i class="fad fa-chart-line fa-fw"></i><span class="link-text"> Dashboard</span></a>
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

							@if( Auth::user() )
								<li class="nav-item nav-heading">Singers</li>
								<li class="nav-item">
									@can('create', \App\Models\Singer::class)
									<a href="#collapse-singers" class="nav-link {{ ( request()->routeIs('singers.*', 'voice-parts.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-users fa-fw"></i><span class="link-text"> Singers</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-singers" class="collapse pl-2 small {{ ( request()->routeIs('singers.*', 'voice-parts.*') ) ? 'show' : '' }}">
										<a href="{{ route('singers.index') }}" class="nav-link {{ ( request()->routeIs('singers.index') ) ? 'active' : '' }}"><i class="fad fa-users fa-fw"></i><span class="link-text"> All Singers</span></a>
										<a href="{{route( 'singers.create' )}}" class="nav-link {{ ( request()->routeIs('singers.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-user-plus"></i><span class="link-text"> Add New</span></a>
										<a href="{{ route('voice-parts.index') }}" class="nav-link {{ ( request()->routeIs('voice-parts.*') ) ? 'active' : '' }}"><i class="fad fa-users-class fa-fw"></i><span class="link-text"> Voice Parts</span></a>
									</div>
									@else
									<a href="{{ route('singers.index') }}" class="nav-link {{ ( request()->routeIs('singers.*') ) ? 'active' : '' }}"><i class="fad fa-users fa-fw"></i><span class="link-text"> Singers</span></a>
									@endcan
								</li>
								<li class="nav-item">
									@can('create', \App\Models\Song::class)
									<a href="#collapse-songs" class="nav-link {{ ( request()->routeIs('songs.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-list-music fa-fw"></i><span class="link-text"> Songs</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-songs" class="collapse pl-2 small {{ ( request()->routeIs('songs.*') ) ? 'show' : '' }}">
										<a href="{{ route('songs.index') }}" class="nav-link {{ ( request()->routeIs('songs.index') ) ? 'active' : '' }}"><i class="fad fa-list-music fa-fw"></i><span class="link-text"> All Songs</span></a>
										<a href="{{route( 'songs.create' )}}" class="nav-link {{ ( request()->routeIs('songs.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-plus-square"></i><span class="link-text"> Add New</span></a>
									</div>
									@else
									<a href="{{ route('songs.index') }}" class="nav-link {{ ( request()->routeIs('songs.*') ) ? 'active' : '' }}"><i class="fad fa-list-music fa-fw"></i><span class="link-text"> Songs</span></a>
									@endcan
								</li>
								<li class="nav-item">
									@can('create', \App\Models\Event::class)
									<a href="#collapse-events" class="nav-link {{ ( request()->routeIs('events.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-calendar-alt fa-fw"></i><span class="link-text"> Events</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-events" class="collapse pl-2 small {{ ( request()->routeIs('events.*') ) ? 'show' : '' }}">
										<a href="{{ route('events.index') }}" class="nav-link {{ ( request()->routeIs('events.index') ) ? 'active' : '' }}"><i class="fad fa-calendar-alt fa-fw"></i><span class="link-text"> All Events</span></a>
										<a href="{{route( 'events.create' )}}" class="nav-link {{ ( request()->routeIs('events.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-plus-square"></i><span class="link-text"> Add New</span></a>
									</div>
									@else
									<a href="{{ route('events.index') }}" class="nav-link {{ ( request()->routeIs('events.*') ) ? 'active' : '' }}"><i class="fad fa-calendar-alt fa-fw"></i><span class="link-text"> Events</span></a>
									@endcan	
								</li>
								<li class="nav-item">
									@can('create', \App\Models\Folder::class)
									<a href="#collapse-folders" class="nav-link {{ ( request()->routeIs('folders.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-folders fa-fw"></i><span class="link-text"> Documents</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-folders" class="collapse pl-2 small {{ ( request()->routeIs('folders.*') ) ? 'show' : '' }}">
										<a href="{{ route('folders.index') }}" class="nav-link {{ ( request()->routeIs('folders.index') ) ? 'active' : '' }}"><i class="fad fa-folders fa-fw"></i><span class="link-text"> All Folders</span></a>
										<a href="{{route( 'folders.create' )}}" class="nav-link {{ ( request()->routeIs('folders.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-plus-square"></i><span class="link-text"> Add Folder</span></a>
									</div>
									@else
									<a href="{{ route('folders.index') }}" class="nav-link {{ ( request()->routeIs('folders.*') ) ? 'active' : '' }}"><i class="fad fa-folders fa-fw fa-swap-opacity fa-swap-color"></i><span class="link-text"> Documents</span></a>
									@endcan
								</li>
								<li class="nav-item">
									@can('create', \App\Models\RiserStack::class)
									<a href="#collapse-stacks" class="nav-link {{ ( request()->routeIs('stacks.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-people-arrows fa-fw"></i><span class="link-text"> Riser Stacks</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-stacks" class="collapse pl-2 small {{ ( request()->routeIs('stacks.*') ) ? 'show' : '' }}">
										<a href="{{ route('stacks.index') }}" class="nav-link {{ ( request()->routeIs('stacks.index') ) ? 'active' : '' }}"><i class="fad fa-people-arrows fa-fw"></i><span class="link-text"> All Stacks</span></a>
										<a href="{{route( 'stacks.create' )}}" class="nav-link {{ ( request()->routeIs('stacks.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-plus-square"></i><span class="link-text"> Add New</span></a>
									</div>
									@else
									<a href="{{ route('stacks.index') }}" class="nav-link {{ ( request()->routeIs('stacks.*') ) ? 'active' : '' }}"><i class="fad fa-people-arrows fa-fw"></i><span class="link-text"> Riser Stacks</span></a>
									@endcan
								</li>
							@endif

							@if( Auth::user()->hasRole('Admin') )
								<li class="nav-item nav-heading">Management</li>
								<li class="nav-item">
									<a href="#collapse-groups" class="nav-link {{ ( request()->routeIs('groups.*') ) ? 'active' : 'collapsed' }} d-flex justify-content-between align-items-center" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapse-singers">
										<span><i class="fad fa-mail-bulk fa-fw"></i><span class="link-text"> Mailing Lists</span></span>
										<i class="far fa-fw menu-chevron"></i>
									</a>
									<div id="collapse-groups" class="collapse pl-2 small {{ ( request()->routeIs('groups.*') ) ? 'show' : '' }}">
										<a href="{{ route('groups.index') }}" class="nav-link {{ ( request()->routeIs('groups.index') ) ? 'active' : '' }}"><i class="fad fa-mail-bulk fa-fw"></i><span class="link-text"> All Lists</span></a>
										<a href="{{route( 'groups.create' )}}" class="nav-link {{ ( request()->routeIs('groups.create') ) ? 'active' : '' }}"><i class="fad fa-fw fa-plus-square"></i><span class="link-text"> Add New</span></a>
									</div>
								</li>
								<li class="nav-item">
									<a href="{{ route('tasks.index') }}" class="nav-link {{ ( request()->routeIs('tasks.*') ) ? 'active' : '' }}"><i class="fad fa-tasks fa-fw"></i><span class="link-text"> Onboarding</span></a>
								</li>
							@endif

							<!--
							<li class="nav-item nav-heading">Account</li>


							<li class="nav-item">
								<a href="{{ route('singers.show', ['singer' => Auth::user()->singer] ) }}" class="nav-link">
									<img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ Auth::user()->name }}" class="user-avatar user-avatar-nav"> <span class="link-text"> {{ Auth::user()->name }}</span>
								</a>
							</li>

							<li class="nav-item">
								<a href="https://headwayapp.co/choir-concierge-updates?utm_medium=widget" target="_blank" id="changelog-link" class="nav-link"><i class="fad fa-fw fa-code"></i> <span class="link-text">Updates </span><span class="headway-badge"></span></a>
							</li>

							<li class="nav-item">
								<a href="{{ route('logout') }}"
								   class="nav-link {{ ( request()->routeIs('logout') ) ? 'active' : '' }}"
								   onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
									<i class="fad fa-sign-out-alt fa-fw"></i> <span class="link-text">Logout</span>
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
							</li>
							-->

						@endguest

						<!--
						<li class="nav-item">
							<a href="" class="nav-link nav-collapse-link"><i class="fa fa-fw fa-caret-left"></i><span class="link-text"> Collapse Menu</span></a>
						</li>-->
					</ul>

				</nav>

			</header>

			<main>
				<nav id="top-menu" class="navbar navbar-expand navbar-light bg-transparent">
					<div class="navbar-brand text-muted">
						<img src="/img/logo.png" alt="Choir Concierge" height="30" class="d-inline-block align-top">
					</div>

					<div class="d-flex justify-content-between flex-grow-1">
						<div></div>
						<ul class="navbar-nav ml-auto">
							@guest
							@else
								<li class="nav-item dropdown">
									<a href="#" class="nav-link dropdown-toggle" id="profile-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ Auth::user()->name }}" class="user-avatar user-avatar-nav"> <span class="link-text"> {{ Auth::user()->name }}</span>
									</a>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
										<a href="{{ route('singers.show', ['singer' => Auth::user()->singer] ) }}" class="dropdown-item"><i class="fal fa-fw fa-user"></i> Edit Profile</a>
										<a href="https://headwayapp.co/choir-concierge-updates?utm_medium=widget" target="_blank" id="changelog-link" class="dropdown-item"><i class="fal fa-fw fa-code"></i> <span class="link-text">Updates </span><span class="headway-badge"></span></a>

										<div class="dropdown-item">
											<a href="{{ route('logout') }}"
											   onclick="event.preventDefault();
										 document.getElementById('logout-form').submit();">
												<i class="fal fa-sign-out-alt fa-fw"></i> <span class="link-text">Logout</span>
											</a>
											<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
												{{ csrf_field() }}
											</form>
										</div>
									</div>
								</li>
							@endguest
						</ul>
					</div>
				</nav>
				@yield('content')
			</main>
		</div>

		<div id="app-bottom">
			@guest
			@else
				<nav class="tap-bar">
					<ul class="nav nav-pills nav-fill">
						@if( Auth::user()->isEmployee() )
							<li class="nav-item">
								<a href="{{ route('singers.index') }}" class="nav-link {{ ( \Request::is('singers', 'singers/*') ) ? 'active' : '' }}"><i class="fad fa-users fa-fw"></i><span class="link-text"> Singers</span></a>
							</li>
							<li class="nav-item">
								<a href="{{ route('songs.index') }}" class="nav-link {{ ( \Request::is('songs', 'songs/*') ) ? 'active' : '' }}"><i class="fad fa-list-music fa-fw"></i><span class="link-text"> Songs</span></a>
							</li>
							<li class="nav-item">
								<a href="{{ route('events.index') }}" class="nav-link {{ ( \Request::is('events', 'events/*') ) ? 'active' : '' }}"><i class="fad fa-calendar-alt fa-fw"></i><span class="link-text"> Events</span></a>
							</li>
						@endif
						<li class="nav-item">
							<a href="" class="nav-link mobile-nav-collapse-link"><i class="fad fa-fw fa-ellipsis-v"></i><span class="link-text"> More</span></a>
						</li>
					</ul>
				</nav>
			@endguest
		</div>
    </div>

    <!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
	
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
