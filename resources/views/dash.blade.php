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

            {{--
            @if( Auth::user()->hasRole('Membership Team') )
            <a href="{{ route('memberprofile.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Member Profile</a>
            @endif
            @if( Auth::user()->hasRole('Music Team') )
            <a href="{{ route('voiceplacement.new') }}" class="list-group-item list-group-item-action" target="_blank"><i class="fa fa-plus"></i> Voice Placement</a>
            @endif--}}
            @if( Auth::user() )
                <a href="{{ route('singers.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-users fa-fw"></i><span class="link-text"> Singers</span></a>
                <a href="{{ route('songs.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-list-music fa-fw"></i><span class="link-text"> Songs</span></a>
                <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-calendar-alt fa-fw"></i><span class="link-text"> Events</span></a>
                <a href="{{ route('folders.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-folders fa-fw fa-swap-opacity fa-swap-color"></i><span class="link-text"> Documents</span></a>
                <a href="{{ route('stacks.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-people-arrows fa-fw"></i><span class="link-text"> Riser Stacks</span></a>
            @endif

            @if( Auth::user()->hasRole('Admin') )
                <a href="{{ route('groups.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-mail-bulk fa-fw"></i><span class="link-text"> Mailing Lists</span></a>
                <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action"><i class="fad fa-tasks fa-fw"></i><span class="link-text"> Onboarding</span></a>
            @endif
      </div>
    </div>
@endsection