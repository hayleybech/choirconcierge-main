@extends('layouts.page')

@section('title', 'Dashboard')

@section('page-title')
<i class="fal fa-chart-line fa-fw"></i> Dashboard
@endsection

@section('page-content')

    <div class="card">
      <div class="card-header">
          <h3 class="h4">Main Menu</h3>
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
            @can('viewAny', \App\Models\Singer::class)
                <a href="{{ route('singers.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-users fa-fw"></i> <span class="link-text"> Singers</span></a>
            @endcan
            @can('viewAny', \App\Models\Song::class)
                <a href="{{ route('songs.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-list-music fa-fw"></i> <span class="link-text"> Songs</span></a>
            @endcan
            @can('viewAny', \App\Models\Event::class)
                <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-calendar-alt fa-fw"></i> <span class="link-text"> Events</span></a>
            @endcan
            @can('viewAny', \App\Models\Folder::class)
                <a href="{{ route('folders.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-folders fa-fw"></i> <span class="link-text"> Documents</span></a>
            @endcan
            @can('viewAny', \App\Models\RiserStack::class)
                <a href="{{ route('stacks.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-people-arrows fa-fw"></i> <span class="link-text"> Riser Stacks</span></a>
            @endcan
            @can('viewAny', \App\Models\UserGroup::class)
                <a href="{{ route('groups.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-mail-bulk fa-fw"></i> <span class="link-text"> Mailing Lists</span></a>
            @endcan
            @can('viewAny', \App\Models\Task::class)
                <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action"><i class="fal fa-tasks fa-fw"></i> <span class="link-text"> Onboarding</span></a>
            @endcan
      </div>
    </div>
@endsection