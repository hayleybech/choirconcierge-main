@extends('layouts.page')

@section('title', $group->title . ' - Mailing Lists')
@section('page-title', $group->title)
@section('page-action')
    @can('update', $group)
    <a href="{{route( 'groups.edit', ['group' => $group] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endcan
@endsection
@section('page-lead')
    <span class="badge badge-light"><i class="{{ (( $group->list_type === 'chat' )) ? 'fa fa-fw fa-comments' : '' }}"></i> {{ ucwords($group->list_type) }}</span><br>
    {{ $group->slug }}{{ '@'.Request::gethost() }}<br>
@endsection

@section('page-content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Recipients</h3>

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
        </div><!-- /.col -->

        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Senders</h3>

                @if( $group->senders->count() > 0 )
                    <ul class="list-group list-group-flush">
                        @foreach($group->senders as $sender)
                            <li class="list-group-item">
                                <strong>
                                    @if($sender->sender_type === \App\Models\Role::class)
                                        Role:
                                    @elseif($sender->sender_type === \App\Models\VoicePart::class)
                                        Voice Part:
                                    @elseif($sender->sender_type === \App\Models\SingerCategory::class)
                                        Filter by Singer Category:
                                    @elseif($sender->sender_type === \App\Models\User::class)
                                        User:
                                    @endif
                                </strong> {{ $sender->sender->name ?? $sender->sender->title ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->

@endsection