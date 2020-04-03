@extends('layouts.page')

@section('title', 'Edit - ' . $group->title)
@section('page-title', 'Edit')

@section('page-content')
    {{ Form::open( array( 'route' => ['groups.update', $group->id], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Group Details</h3>

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

                <div class="custom-control custom-radio">
                    <input id="list_type_chat" name="list_type" value="chat" class="custom-control-input" type="radio" {{ ($group->list_type === 'chat' ) ? 'checked' : '' }}>
                    <label for="list_type_chat" class="custom-control-label"><i class="fa fa-fw fa-comments"></i> Chat</label>
                    <small class="form-text text-muted ml-2">
                        Chat lists allow all recipients to reply-all, and take part in a discussion.
                    </small>
                </div>

                <div class="custom-control custom-radio">
                    <input id="list_type_distribution" name="list_type" value="distribution" class="custom-control-input" type="radio" {{ ($group->list_type === 'distribution' ) ? 'checked' : '' }} disabled>
                    <label for="list_type_distribution" class="custom-control-label"><i class="fa fa-fw fa-bullhorn"></i> Distribution (Coming soon)</label>
                    <small class="form-text text-muted ml-2">
                        Distribution lists only allow the original sender or list owner(s) to reply-all.
                    </small>
                </div>

                <div class="custom-control custom-radio">
                    <input id="list_type_public" name="list_type" value="public" class="custom-control-input" type="radio" {{ ($group->list_type === 'public' ) ? 'checked' : '' }} disabled>
                    <label for="list_type_public" class="custom-control-label"><i class="fa fa-fw fa-inbox"></i> Public (Coming soon)</label>
                    <small class="form-text text-muted ml-2">
                        Public lists are used like regular email addresses.
                    </small>
                </div>

            </fieldset>

        </div>
    </div>

    <div class="card bg-light">

        <h3 class="card-header h4">Recipients</h3>

        <div class="card-body">
            <div class="form-group">
                <label for="recipient_roles"><i class="fa fa-fw fa-users"></i> Roles</label><br>
                <select id="recipient_roles" name="recipient_roles[]" class="select2 form-control" data-recipient-type="roles" multiple>
                    @foreach($roles as $role)
                        <option value="{{$role->memberable_id}}" selected>{{$role->memberable->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="recipient_users"><i class="fa fa-fw fa-user"></i> Users</label><br>
                <select id="recipient_users" name="recipient_users[]" class="select2 form-control" data-recipient-type="users" multiple>
                    @foreach($users as $user)
                        <option value="{{$user->memberable_id}}" selected>{{$user->memberable->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection