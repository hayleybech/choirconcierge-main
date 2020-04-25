@extends('layouts.page')

@section('title', 'Dashboard')

@section('page-title')
<i class="fal fa-chart-line fa-fw"></i> Dashboard
@endsection

@section('page-content')

    <div class="card bg-light">
      <div class="card-header">
        Main Menu
      </div>
      <div class="list-group list-group-flush">
          <!--<a href="#" class="list-group-item list-group-item-action">
            Blenders Audition Songs
          </a>
          <a href="#" class="list-group-item list-group-item-action">The Blenders Facebook Chat (New)</a>-->

            @if( Auth::user()->hasRole('Admin') )
            <a href="{{ route('groups.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-users"></i> Groups List</a>
            <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-tasks"></i> Tasks List</a>
            @endif

            {{--
            @if( Auth::user()->hasRole('Membership Team') )
            <a href="{{ route('memberprofile.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Member Profile</a>
            @endif
            @if( Auth::user()->hasRole('Music Team') )
            <a href="{{ route('voiceplacement.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Voice Placement</a>
            @endif--}}
            @if( Auth::user() )
            <a href="{{ route('singers.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-user"></i> Singers List</a>
            <a href="{{ route('songs.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-music"></i> Songs List</a>
            <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action"><i class="fa fa-calendar"></i> Events List</a>
            @endif
      </div>
    </div>
@endsection