@extends('layouts.app')

@section('app-content')
    <div id="app-top">

        <header id="app-header">

            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="" href="{{ url('/') }}">
                    <img src="{{ global_asset('/img/logo.svg') }}" alt="Choir Concierge" class="logo">
                    <img src="{{ global_asset('/favicon.png') }}" alt="Choir Concierge" class="logo-collapse">
                </a>

                <div class="choir-logo navbar-brand text-muted">
                    <img src="{{ asset('choir-logo.png') }}" alt="{{ tenant('choir_name') ?? 'Choir Name' }}" height="30" class="d-inline-block align-top">
                </div>

                <ul class="navbar-nav nav-vertical accordion" id="main-menu-accordion">

                    <li class="nav-item nav-facade-top">
                        <span class="nav-link"></span>
                    </li>

                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item {{ ( request()->routeIs('login') ) ? 'active' : '' }}">
                            <a href="{{ route('login') }}" class="nav-link {{ ( request()->routeIs('login') ) ? 'active' : '' }}"><i class="fal fa-sign-in-alt fa-fw"></i> Login</a>
                        </li>
                        {{--
                        <li class="nav-item {{ ( request()->routeIs('register') ) ? 'active' : '' }}">
                            <a href="{{ route('register') }}" class="nav-link"><i class="fa fa-user-plus fa-fw"></i> Register</a>
                        </li>--}}
                    @else

                        <li class="nav-item {{ ( request()->routeIs('dash') ) ? 'active' : '' }}">
                            <a class="nav-link {{ ( request()->routeIs('dash') ) ? 'active' : '' }}" href="{{ route('dash') }}"><i class="fal fa-chart-line fa-fw"></i><span class="link-text">Dashboard</span></a>
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

                        @can('viewAny', \App\Models\Singer::class)
                            <li class="nav-item {{ ( request()->routeIs('singers.*', 'voice-parts.*', 'roles.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('singers.index') }}" class="nav-link {{ ( request()->routeIs('singers.*') ) ? 'active' : '' }}"><i class="fal fa-users fa-fw"></i><span class="link-text">Singers</span></a>
                                @can('create', \App\Models\Singer::class)
                                    <div id="collapse-singers" class="submenu collapse small {{ ( request()->routeIs('singers.*', 'voice-parts.*', 'roles.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('singers.index') }}" class="nav-link {{ ( request()->routeIs('singers.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Singers</span></a>
                                        <a href="{{route( 'singers.create' )}}" class="nav-link {{ ( request()->routeIs('singers.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                        @can('viewAny', \App\Models\VoicePart::class)<a href="{{ route('voice-parts.index') }}" class="nav-link {{ ( request()->routeIs('voice-parts.*') ) ? 'active' : '' }}"><i class="fal fa-users-class fa-fw"></i><span class="link-text">Voice Parts</span></a>@endcan
                                        @can('viewAny', \App\Models\Role::class)<a href="{{ route('roles.index') }}" class="nav-link {{ ( request()->routeIs('roles.*') ) ? 'active' : '' }}"><i class="fal fa-user-tag fa-fw"></i><span class="link-text">Roles</span></a>@endcan
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\Song::class)
                            <li class="nav-item {{ ( request()->routeIs('songs.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('songs.index') }}" class="nav-link {{ ( request()->routeIs('songs.*') ) ? 'active' : '' }}"><i class="fal fa-list-music fa-fw"></i><span class="link-text">Songs</span></a>
                                @can('create', \App\Models\Song::class)
                                    <div id="collapse-songs" class="submenu collapse small {{ ( request()->routeIs('songs.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('songs.index') }}" class="nav-link {{ ( request()->routeIs('songs.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Songs</span></a>
                                        <a href="{{route( 'songs.create' )}}" class="nav-link {{ ( request()->routeIs('songs.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\Event::class)
                            <li class="nav-item  {{ ( request()->routeIs('events.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('events.index') }}" class="nav-link {{ ( request()->routeIs('events.*') ) ? 'active' : '' }}"><i class="fal fa-calendar-alt fa-fw"></i><span class="link-text">Events</span></a>
                                @can('create', \App\Models\Event::class)
                                    <div id="collapse-events" class="submenu collapse small {{ ( request()->routeIs('events.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('events.index') }}" class="nav-link {{ ( request()->routeIs('events.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Events</span></a>
                                        <a href="{{route( 'events.create' )}}" class="nav-link {{ ( request()->routeIs('events.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                        @can('viewAny', \App\Models\Attendance::class)
                                            <a href="{{ route( 'events.reports.attendance' ) }}" class="nav-link {{ ( request()->routeIs('events.reports.attendance') ) ? 'active' : '' }}"><i class="fal fa-fw fa-analytics"></i><span class="link-text">Attendance Report</span></a>
                                        @endcan
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\Folder::class)
                            <li class="nav-item {{ ( request()->routeIs('folders.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('folders.index') }}" class="nav-link {{ ( request()->routeIs('folders.*') ) ? 'active' : '' }}"><i class="fal fa-folders fa-fw"></i><span class="link-text">Documents</span></a>
                                @can('create', \App\Models\Folder::class)
                                    <div id="collapse-folders" class="submenu collapse small {{ ( request()->routeIs('folders.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('folders.index') }}" class="nav-link {{ ( request()->routeIs('folders.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Folders</span></a>
                                        <a href="{{route( 'folders.create' )}}" class="nav-link {{ ( request()->routeIs('folders.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add Folder</span></a>
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\RiserStack::class)
                            <li class="nav-item {{ ( request()->routeIs('stacks.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('stacks.index') }}" class="nav-link {{ ( request()->routeIs('stacks.*') ) ? 'active' : '' }}"><i class="fal fa-people-arrows fa-fw"></i><span class="link-text">Riser Stacks</span></a>
                                @can('create', \App\Models\RiserStack::class)
                                    <div id="collapse-stacks" class="submenu collapse small {{ ( request()->routeIs('stacks.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('stacks.index') }}" class="nav-link {{ ( request()->routeIs('stacks.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Stacks</span></a>
                                        <a href="{{route( 'stacks.create' )}}" class="nav-link {{ ( request()->routeIs('stacks.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\UserGroup::class)
                            <li class="nav-item {{ ( request()->routeIs('groups.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('groups.index') }}" class="nav-link {{ ( request()->routeIs('groups.*') ) ? 'active' : '' }}"><span><i class="fal fa-mail-bulk fa-fw"></i><span class="link-text">Mailing Lists</span></span></a>
                                @can('create', \App\Models\UserGroup::class)
                                    <div id="collapse-groups" class="submenu collapse small {{ ( request()->routeIs('groups.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('groups.index') }}" class="nav-link {{ ( request()->routeIs('groups.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Lists</span></a>
                                        <a href="{{route( 'groups.create' )}}" class="nav-link {{ ( request()->routeIs('groups.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                    </div>
                                @endcan
                            </li>
                        @endcan
                        @can('viewAny', \App\Models\Task::class)
                            <li class="nav-item {{ ( request()->routeIs('tasks.*') ) ? 'active' : 'collapsed' }}">
                                <a href="{{ route('tasks.index') }}" class="nav-link {{ ( request()->routeIs('tasks.*') ) ? 'active' : 'collapsed' }}"><span><i class="fal fa-tasks fa-fw"></i><span class="link-text">Onboarding</span></span></a>
                                @can('create', \App\Models\Task::class)
                                    <div id="collapse-tasks" class="submenu collapse small {{ ( request()->routeIs('tasks.*') ) ? 'show' : '' }}" data-parent="#main-menu-accordion">
                                        <a href="{{ route('tasks.index') }}" class="nav-link {{ ( request()->routeIs('tasks.index') ) ? 'active' : '' }}"><i class="fal fa-list fa-fw"></i><span class="link-text">All Tasks</span></a>
                                        <a href="{{route( 'tasks.create' )}}" class="nav-link {{ ( request()->routeIs('tasks.create') ) ? 'active' : '' }}"><i class="fal fa-fw fa-plus-square"></i><span class="link-text">Add New</span></a>
                                    </div>
                                @endcan
                            </li>
                        @endcan

                    <!--
							<li class="nav-item nav-heading">Account</li>


							<li class="nav-item">
								<a href="{{ route('singers.show', ['singer' => Auth::user()->singer] ) }}" class="nav-link">
									<img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ Auth::user()->name }}" class="user-avatar user-avatar-nav"> <span class="link-text">{{ Auth::user()->name }}</span>
								</a>
							</li>

							<li class="nav-item">
								<a href="https://headwayapp.co/choir-concierge-updates?utm_medium=widget" target="_blank" id="changelog-link" class="nav-link"><i class="fal fa-fw fa-code"></i> <span class="link-text">Updates </span><span class="headway-badge"></span></a>
							</li>

							<li class="nav-item">
								<a href="{{ route('logout') }}"
								   class="nav-link {{ ( request()->routeIs('logout') ) ? 'active' : '' }}"
								   onclick="event.preventDefault();
									 document.getElementById('logout-form').submit();">
									<i class="fal fa-sign-out-alt fa-fw"></i> <span class="link-text">Logout</span>
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
                            </form>
                        </li>
-->

                    @endguest

                    <li class="nav-item nav-facade-bottom">
                        <span class="nav-link"></span>
                    </li>

                    <!--
                    <li class="nav-item">
                        <a href="" class="nav-link nav-collapse-link"><i class="fa fa-fw fa-caret-left"></i><span class="link-text">Collapse Menu</span></a>
                    </li>-->
                </ul>

            </nav>

        </header>

        <main>
            <nav id="top-menu" class="navbar navbar-expand navbar-light bg-transparent">

                <div class="d-flex justify-content-between align-items-center flex-grow-1">

                    {{ Breadcrumbs::render() }}

                    <ul class="navbar-nav ml-auto">
                        @guest
                        @else
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle @if( session()->has('impersonation:active') ) bg-danger text-white @endif" id="profile-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ Auth::user()->name }}" class="user-avatar user-avatar-nav"> <span class="link-text">{{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a href="{{ route('singers.show', ['singer' => Auth::user()->singer] ) }}" class="dropdown-item"><i class="fal fa-fw fa-user"></i> Edit Profile</a>
                                    <a href="https://headwayapp.co/choir-concierge-updates?utm_medium=widget" target="_blank" id="changelog-link" class="dropdown-item"><i class="fal fa-fw fa-code"></i> <span class="link-text">Updates </span><span class="headway-badge"></span></a>

                                    @if( Auth::user()->hasRole('Admin') && ! session()->has('impersonation:active') )
                                        <!-- Impersonate User modal trigger button -->
                                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#impersonateUserModal"><i class="fal fa-user-unlock"></i> Impersonate User</a>
                                    @endif
                                    @if( session()->has('impersonation:active') )
                                        <a href="{{ route('impersonation.stop') }}" class="dropdown-item bg-danger text-white"><i class="fal fa-user-lock"></i> Stop Impersonating</a>
                                    @endif

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
            @yield('tenant-content')
        </main>
    </div>

    <div id="app-bottom">
        @guest
        @else
            <nav class="tap-bar">
                <ul class="nav nav-pills nav-fill">
                    @can('ViewAny', \App\Models\Singer::class)
                        <li class="nav-item">
                            <a href="{{ route('singers.index') }}" class="nav-link {{ ( \Request::is('singers', 'singers/*') ) ? 'active' : '' }}"><i class="fal fa-users fa-fw mr-2"></i><span class="link-text">Singers</span></a>
                        </li>
                    @endcan
                    @can('viewAny', \App\Models\Song::class)
                        <li class="nav-item">
                            <a href="{{ route('songs.index') }}" class="nav-link {{ ( \Request::is('songs', 'songs/*') ) ? 'active' : '' }}"><i class="fal fa-list-music fa-fw mr-2"></i><span class="link-text">Songs</span></a>
                        </li>
                    @endcan
                    @can('viewAny', \App\Models\Event::class)
                        <li class="nav-item">
                            <a href="{{ route('events.index') }}" class="nav-link {{ ( \Request::is('events', 'events/*') ) ? 'active' : '' }}"><i class="fal fa-calendar-alt fa-fw mr-2"></i><span class="link-text">Events</span></a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a href="" class="nav-link mobile-nav-collapse-link"><i class="fal fa-fw fa-ellipsis-v"></i><span class="link-text">More</span></a>
                    </li>
                </ul>
            </nav>
        @endguest
    </div>

    <impersonate-user-modal route="{{ route('users.impersonate', '--replace--') }}"></impersonate-user-modal>

@endsection
