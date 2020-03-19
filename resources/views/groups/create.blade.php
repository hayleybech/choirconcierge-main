@extends('layouts.app')

@section('title', 'Add Group')

@section('content')

    <h2 class="display-4 mb-4">Add Group</h2>

    @include('partials.flash')

    {{ Form::open( array( 'route' => 'groups.index' ) ) }}

    <div class="form-group">
        {{ Form::label('title', 'Title') }}
        {{ Form::text('title', '', array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('slug', 'Slug') }}
        <div class="input-group mb-3">
            {{ Form::text('slug', '', array('class' => 'form-control')) }}
            <div class="input-group-append">
                <span class="input-group-text" id="basic-addon2">{{ '@'.Request::gethost() }}</span>
            </div>
        </div>
    </div>

    <fieldset class="form-group">
        <legend class="col-form-label">List Type</legend>

        <div class="custom-control custom-radio custom-control-inline">
            <input id="list_type_chat" name="list_type" value="chat" class="custom-control-input" type="radio" checked>
            <label for="list_type_chat" class="custom-control-label"><i class="fa fa-fw fa-comments"></i> Chat</label>
            <small class="form-text text-muted ml-2">
                Chat lists allow all recipients to reply-all, and take part in a discussion.
            </small>
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input id="list_type_distribution" name="list_type" value="distribution" class="custom-control-input" type="radio" disabled>
            <label for="list_type_distribution" class="custom-control-label"><i class="fa fa-fw fa-bullhorn"></i> Distribution (Coming soon)</label>
            <small class="form-text text-muted ml-2">
                Distribution lists only allow the original sender or list owner(s) to reply-all.
            </small>
        </div>

        <div class="custom-control custom-radio custom-control-inline">
            <input id="list_type_public" name="list_type" value="public" class="custom-control-input" type="radio" disabled>
            <label for="list_type_public" class="custom-control-label"><i class="fa fa-fw fa-inbox"></i> Public (Coming soon)</label>
            <small class="form-text text-muted ml-2">
                Public lists are used like regular email addresses.
            </small>
        </div>

    </fieldset>

    <h4>Recipients</h4>

    <div class="form-group">
        <label for="recipient_roles"><i class="fa fa-fw fa-users"></i> Roles</label>
        <select id="recipient_roles" name="recipient_roles[]" class="select2 form-control" data-recipient-type="roles" multiple></select>
    </div>

    <div class="form-group">
        <label for="recipient_users"><i class="fa fa-fw fa-user"></i> Users</label>
        <select id="recipient_users" name="recipient_users[]" class="select2 form-control" data-recipient-type="users" multiple></select>
    </div>

    {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection