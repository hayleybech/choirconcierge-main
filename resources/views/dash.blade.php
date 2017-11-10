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
	  <a href="{{ route('memberprofile.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Member Profile</a>
	  <a href="{{ route('memberprofile.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Voice Placement</a>
	  <a href="{{ route('singers.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-list-alt"></i> Singers List</a>
  </div>
</div>
@endsection