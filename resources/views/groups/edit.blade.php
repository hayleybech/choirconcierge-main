@extends('layouts.page')

@section('title', 'Edit - ' . $group->title)
@section('page-title', $group->title)

@section('page-content')
    {{ Form::open( [ 'route' => ['groups.update', $group->id], 'method' => 'put' ] ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">List Details</h3>

                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('title', 'Title') }}
                        {{ Form::text('title', $group->title, ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('slug', 'Slug') }}
                        <div class="input-group mb-3">
                            {{ Form::text('slug', $group->slug, ['class' => 'form-control']) }}
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
                                        <strong>Example: </strong> Director<br>
                                        The general public can send to this address, and all recipients can respond.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_chat" class="btn btn-outline-dark py-3 px-3 text-left d-flex align-items-center {{ ($group->list_type === 'chat' ) ? 'active' : '' }}">
                                <i class="fa fa-fw fa-comments fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_chat" name="list_type" value="chat" type="radio" autocomplete="off" {{ ($group->list_type === 'chat' ) ? 'checked' : '' }}>
                                    <span class="h5">Chat</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong> Internal communication for teams/groups.<br>
                                        <strong>Example: </strong> Music Team<br>
                                        Recipients are able to reply to all other recipients, and can compose new emails to the group.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_distribution" class="btn btn-outline-dark py-3 px-3 text-left d-flex align-items-center {{ ($group->list_type === 'distribution' ) ? 'active' : '' }}">
                                <i class="fa fa-fw fa-paper-plane fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_distribution" name="list_type" value="distribution" type="radio" autocomplete="off" {{ ($group->list_type === 'distribution' ) ? 'checked' : '' }}>
                                    <span class="h5">Mailout</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong>Notifications, newsletters, reminders, etc.<br>
                                        <strong>Example: </strong> Active Members<br>
                                        Recipients can see the emails and can reply to the sender, but cannot "reply-all".
                                    </span>
                                </span>
                            </label>

                        </div>

                    </fieldset>

                </div>
            </div>

        </div><!--- /.col -->
    </div><!-- /.row -->

    <div class="row">
        <div class="col-md-6">

            <div class="card">

                <h3 class="card-header h4">Recipients</h3>

                <div class="card-body">

                    <div class="form-group">
                        <label for="recipient_roles">Roles</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                            </div>
                            <select id="recipient_roles" name="recipient_roles[]" class="select2 custom-select" data-model="roles" multiple>
                            @foreach($group->recipient_roles as $role)
                                <option value="{{$role->id}}" selected>{{$role->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_voice_parts">Voice Parts</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users-class"></i></span>
                            </div>
                            <select id="recipient_voice_parts" name="recipient_voice_parts[]" class="select2 form-control" data-model="voice_parts" multiple>
                            @foreach($group->recipient_voice_parts as $voice_part)
                                <option value="{{$voice_part->id}}" selected>{{$voice_part->title}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_users">Users</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                            </div>
                            <select id="recipient_users" name="recipient_users[]" class="select2 form-control" data-model="users" multiple>
                            @foreach($group->recipient_users as $user)
                                <option value="{{$user->id}}" selected>{{$user->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_singer_categories">Singer Category</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-circle"></i></span>
                            </div>
                            <select id="recipient_singer_categories" name="recipient_singer_categories[]" class="select2 form-control" data-model="singer_categories" multiple>
                            @foreach($group->recipient_singer_categories as $singer_category)
                                <option value="{{$singer_category->id}}" selected>{{$singer_category->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-6">

            <div class="card">

                <h3 class="card-header h4">Senders</h3>

                <div class="card-body">

                    <div class="form-group">
                        <label for="sender_roles">Roles</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                            </div>
                            <select id="sender_roles" name="sender_roles[]" class="select2 custom-select" data-model="roles" multiple>
                            @foreach($group->sender_roles as $role)
                                <option value="{{$role->id}}" selected>{{$role->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_voice_parts">Voice Parts</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users-class"></i></span>
                            </div>
                            <select id="sender_voice_parts" name="sender_voice_parts[]" class="select2 form-control" data-model="voice_parts" multiple>
                            @foreach($group->sender_voice_parts as $voice_part)
                                <option value="{{$voice_part->id}}" selected>{{$voice_part->title}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_users">Users</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                            </div>
                            <select id="sender_users" name="sender_users[]" class="select2 form-control" data-model="users" multiple>
                            @foreach($group->sender_users as $user)
                                <option value="{{$user->id}}" selected>{{$user->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_singer_categories">Singer Category</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-circle"></i></span>
                            </div>
                            <select id="sender_singer_categories" name="sender_singer_categories[]" class="select2 form-control" data-model="singer_categories" multiple>
                            @foreach($group->sender_singer_categories as $singer_category)
                                <option value="{{$singer_category->id}}" selected>{{$singer_category->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>

        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="mb-5">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-fw fa-check"></i> Save
        </button>
        <a href="{{ route('groups.show', [$group]) }}" class="btn btn-outline-secondary">
            <i class="fa fa-fw fa-times"></i> Cancel
        </a>
    </div>



    {{ Form::close() }}

@endsection