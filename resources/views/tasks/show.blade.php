@extends('layouts.page')

@section('title', $task->name . ' - Tasks')
@section('page-title', $task->name)

@section('page-action')

@endsection

@section('page-lead')
    Role: {{ $task->role->name }}<br>
    Type: {{ $task->type }}<br>
    Created:
    <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($task->created_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($task->created_at)->format('M d, H:i') }}
        </span>
    </span><br>
    Updated: <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($task->updated_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($task->updated_at)->format('M d, H:i') }}
        </span>
    </span><br>
@endsection

@section('page-content')

    <div class="card bg-light">
        <div class="card-header d-flex justify-content-start align-items-center">
            <h3 class="h4 mb-0">Notifications</h3>
            <a href="{{ route( 'tasks.notifications.create', $task ) }}" class="btn btn-add btn-sm btn-secondary ml-2"><i class="fa fa-fw fa-plus"></i> Add Notification</a>
        </div>

        <div class="list-group-flush">
        @foreach($task->notification_templates as $template)
            <div class="list-group-item d-flex justify-content-between">
                <a href="{{ route('tasks.notifications.show', [$task, $template]) }}" class="">{{ $template->subject }}</a>
                <x-delete-button :action="route( 'tasks.notifications.destroy', [$task, $template] )"/>
            </div>
        @endforeach
        </div>
    </div>

@endsection