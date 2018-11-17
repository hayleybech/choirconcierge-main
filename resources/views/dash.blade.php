@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2>Dashboard</h2>

<div class="card">
  <div class="card-header">
    Main Menu
  </div>
  <div class="list-group list-group-flush">
	  <!--<a href="#" class="list-group-item list-group-item-action">
		Blenders Audition Songs
	  </a>
	  <a href="#" class="list-group-item list-group-item-action">The Blenders Facebook Chat (New)</a>-->
	  
		@if( Auth::user()->hasRole('Admin') )
		<a href="{{ route('users.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-group"></i> Users List</a>
		<a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-list-alt"></i> Tasks List</a>
		@endif

	  	{{--
		@if( Auth::user()->hasRole('Membership Team') )
		<a href="{{ route('memberprofile.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Member Profile</a>
		@endif
		@if( Auth::user()->hasRole('Music Team') )
		<a href="{{ route('voiceplacement.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Voice Placement</a>
		@endif--}}
		@if( Auth::user()->isEmployee() )
		<a href="{{ route('singers.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-list-alt"></i> Singers List</a>
		@endif
  </div>
</div>
@endsection