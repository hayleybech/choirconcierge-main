@extends('layouts.page')

@section('title', $group->title . ' - Mailing Lists')
@section('page-title', $group->title)
@section('page-action')
<a href="{{route( 'groups.edit', ['group' => $group] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection
@section('page-lead')
    <span class="badge badge-light"><i class="{{ (( $group->list_type === 'chat' )) ? 'fa fa-fw fa-comments' : '' }}"></i> {{ ucwords($group->list_type) }}</span><br>
    {{ $group->slug }}<br>
@endsection

@section('page-content')
    <div class="card bg-light">
        <h3 class="card-header h4">Members</h3>

        @if( $group->members->count() > 0 )
        <ul class="list-group list-group-flush">
        @foreach($group->members as $member)
            <li class="list-group-item">
                <strong>
                @if($member->memberable_type === \App\Models\Role::class)
                    Role:
                @elseif($member->memberable_type === \App\Models\VoicePart::class)
                    Voice Part:
                @elseif($member->memberable_type === \App\Models\SingerCategory::class)
                    Filter by Singer Category:
                @elseif($member->memberable_type === \App\Models\User::class)
                    User:
                @endif
                </strong> {{ $member->memberable->name ?? $member->memberable->title ?? '' }}
            </li>
        @endforeach
        </ul>
        @endif

    </div>


@endsection