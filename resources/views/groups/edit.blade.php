@extends('layouts.page')

@section('title', 'Edit - ' . $group->title)
@section('page-title', $group->title)

@section('page-content')
    {{ Form::open( array( 'route' => ['groups.update', $group->id], 'method' => 'put' ) ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">List Details</h3>

                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('title', 'Title') }}
                        {{ Form::text('title', $group->title, array('class' => 'form-control')) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('slug', 'Slug') }}
                        <div class="input-group mb-3">
                            {{ Form::text('slug', $group->slug, array('class' => 'form-control')) }}
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">{{ '@'.Request::gethost() }}</span>
                            </div>
                        </div>
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">List Type</legend>

                        <div class="btn-group-vertical btn-group-toggle d-flex bg-white" data-toggle="buttons">

                            <label for="list_type_public" class="btn btn-outline-dark py-3 px-3 text-left d-flex align-items-center {{ ($group->list_type === 'public' ) ? 'active' : '' }}">
                                <i class="fa fa-fw fa-envelope-open-text fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_public" name="list_type" value="public" type="radio" autocomplete="off" {{ ($group->list_type === 'public' ) ? 'checked' : '' }}>
                                    <span class="h5">Public</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong> General Enquiries.<br>
                                        The general public can send to this address, and all recipients can respond.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_chat" class="btn btn-outline-dark py-3 px-3 text-left d-flex align-items-center {{ ($group->list_type === 'chat' ) ? 'active' : '' }} disabled">
                                <i class="fa fa-fw fa-comments fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_chat" name="list_type" value="chat" type="radio" autocomplete="off" {{ ($group->list_type === 'chat' ) ? 'checked' : '' }} disabled>
                                    <span class="h5">Chat (Coming Soon)</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong> Internal communication for teams/groups.<br>
                                        Recipients are able to reply to all other recipients, and can compose new emails to the group.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_distribution" class="btn btn-outline-dark py-3 px-3 text-left d-flex align-items-center {{ ($group->list_type === 'distribution' ) ? 'active' : '' }} disabled">
                                <i class="fa fa-fw fa-paper-plane fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_distribution" name="list_type" value="distribution" type="radio" autocomplete="off" {{ ($group->list_type === 'distribution' ) ? 'checked' : '' }} disabled>
                                    <span class="h5">Mailout (Coming soon)</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong>Notifications, newsletters, reminders, etc.<br>
                                        Recipients can see the emails and can reply to the sender, but cannot "reply-all".
                                    </span>
                                </span>
                            </label>

                        </div>

                    </fieldset>

                </div>
            </div>

            <div class="card">

                <h3 class="card-header h4">Recipients</h3>

                <div class="card-body">
                    <div class="form-group">
                        <label for="recipient_roles"><i class="fa fa-fw fa-users"></i> Roles</label><br>
                        <select id="recipient_roles" name="recipient_roles[]" class="select2 form-control" data-model="roles" multiple>
                            @foreach($group->roles as $role)
                                <option value="{{$role->id}}" selected>{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recipient_voice_parts"><i class="fa fa-fw fa-users-class"></i> Voice Parts</label><br>
                        <select id="recipient_voice_parts" name="recipient_voice_parts[]" class="select2 form-control" data-model="voice_parts" multiple>
                            @foreach($group->voice_parts as $voice_part)
                                <option value="{{$voice_part->id}}" selected>{{$voice_part->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recipient_users"><i class="fa fa-fw fa-user"></i> Users</label><br>
                        <select id="recipient_users" name="recipient_users[]" class="select2 form-control" data-model="users" multiple>
                            @foreach($group->users as $user)
                                <option value="{{$user->id}}" selected>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recipient_singer_categories"><i class="fa fa-fw fa-filter"></i> Filter by Singer Category</label><br>
                        <select id="recipient_singer_categories" name="recipient_singer_categories[]" class="select2 form-control" data-model="singer_categories" multiple>
                            @foreach($group->singer_categories as $singer_category)
                                <option value="{{$singer_category->id}}" selected>{{$singer_category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('groups.show', [$group]) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>
            
        </div>
    </div>

    {{ Form::close() }}

@endsection