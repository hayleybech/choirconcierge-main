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




@endsection