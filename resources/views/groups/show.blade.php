@extends('layouts.app')

@section('title', $group->title . ' - Groups')
@section('page-title', $group->title)
@section('page-action')
<a href="{{route( 'groups.edit', ['group' => $group] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection
@section('page-lead')
    Group Slug: {{ $group->slug }}<br>
    List Type: {{ $group->list_type }}<br>
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
                @elseif($member->memberable_type === \App\Models\User::class)
                    User:
                @endif
                </strong> {{ $member->memberable->name }}
            </li>
        @endforeach
        </ul>
        @endif

    </div>


@endsection