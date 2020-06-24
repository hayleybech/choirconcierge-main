@extends('layouts.page')

@section('title', $notification->subject . ' - Task Notifications')
@section('page-title', $notification->subject)

@section('page-action')
    <a href="{{route( 'tasks.notifications.edit', [$task, $notification] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection

@section('page-lead')
    Recipients: <code>{{ $notification->recipients }}</code><br>
    Delay: {{ $notification->delay }}<br>
    Created:
    <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($notification->created_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($notification->created_at)->format('M d, H:i') }}
        </span>
    </span><br>
    Updated: <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($notification->updated_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($notification->updated_at)->format('M d, H:i') }}
        </span>
    </span><br>
@endsection

@section('page-content')
    <div class="card bg-light">
        <div class="card-header d-flex justify-content-start align-items-center">
            <h3 class="h4 mb-0">Body</h3>
        </div>
        <div class="card-body">
            <p>{!! $notification->bodyWithHighlights !!}</p>
        </div>
    </div>

@endsection