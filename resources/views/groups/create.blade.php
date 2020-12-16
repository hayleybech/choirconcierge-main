@extends('layouts.page')

@section('title', 'Add Mailing List')
@section('page-title', 'Add Mailing List')

@section('page-content')

    {{ Form::open( [ 'route' => 'groups.index' ] ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">List Details</h3>

                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('title', 'Title') }}
                        {{ Form::text('title', '', ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('slug', 'Address') }}
                        <div class="input-group mb-3">
                            {{ Form::text('slug', '', ['class' => 'form-control']) }}
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">{{ '@'.Request::gethost() }}</span>
                            </div>
                        </div>
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">List Type</legend>

                        <div id="list_type" class="btn-group-vertical btn-group-toggle d-flex bg-white" data-toggle="buttons">

                            <label for="list_type_public" class="btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center active">
                                <i class="fa fa-fw fa-envelope-open-text fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_public" name="list_type" value="public" type="radio" autocomplete="off" checked>
                                    <span class="h5">Public</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong> General Enquiries.<br>
                                        <strong>Example: </strong> Director<br>
                                        The general public can send to this address, and all recipients can respond.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_chat" class="btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center">
                                <i class="fa fa-fw fa-comments fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_chat" name="list_type" value="chat" type="radio" autocomplete="off">
                                    <span class="h5">Chat</span>
                                    <span class="form-text">
                                        <strong>Best for: </strong> Internal communication for teams/groups.<br>
                                        <strong>Example: </strong> Music Team<br>
                                        Recipients are able to reply to all other recipients, and can compose new emails to the group.
                                    </span>
                                </span>
                            </label>

                            <label for="list_type_distribution" class="btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center">
                                <i class="fa fa-fw fa-paper-plane fa-2x mr-3"></i>
                                <span>
                                    <input id="list_type_distribution" name="list_type" value="distribution" type="radio" autocomplete="off">
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

                </div><!-- /.card-body -->


            </div><!-- /.card -->

        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <h3 class="card-header h4">Recipients</h3>

                <div class="card-body">

                    <div class="form-group">
                        <label for="recipient_users">Users</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                            </div>
                            <select id="recipient_users" name="recipient_users[]" class="select2 form-control" data-model="users" multiple></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_roles">Roles</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                            </div>
                            <select id="recipient_roles" name="recipient_roles[]" class="select2 custom-select" data-model="roles" multiple></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_voice_parts">Voice Parts</label>

                        <div class="btn-group btn-group-toggle d-flex bg-white" data-toggle="buttons">
                            @foreach($voice_parts as $voice_part)
                                <label for="recipient_voice_parts_{{ $voice_part->id }}" class="btn btn-outline-dark btn-check py-1 px-3 text-left d-flex align-items-center">
                                    <input id="recipient_voice_parts_{{ $voice_part->id }}" name="recipient_voice_parts[]" value="{{ $voice_part->id }}" type="checkbox" autocomplete="off">
                                    <span>{{ $voice_part->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recipient_singer_categories">Singer Category</label>

                        <div class="btn-group btn-group-toggle d-flex bg-white" data-toggle="buttons">
                        @foreach($singer_categories as $category_id => $category_name)
                            <label for="recipient_singer_categories_{{ $category_id }}" class="btn btn-outline-dark btn-check py-1 px-3 text-left d-flex align-items-center">
                                <input id="recipient_singer_categories_{{ $category_id }}" name="recipient_singer_categories[]" value="{{ $category_id }}" type="checkbox" autocomplete="off">
                                <span>{{ $category_name }}</span>
                            </label>
                        @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="col-md-6">

            <div class="card" id="senders">
                <h3 class="card-header h4">Senders</h3>

                <div class="card-body">

                    <div class="form-group">
                        <label for="sender_users">Users</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                            </div>
                            <select id="sender_users" name="sender_users[]" class="select2 form-control" data-model="users" multiple></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_roles">Roles</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                            </div>
                            <select id="sender_roles" name="sender_roles[]" class="select2 custom-select" data-model="roles" multiple></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_voice_parts">Voice Parts</label>

                        <div class="btn-group btn-group-toggle d-flex bg-white" data-toggle="buttons">
                            @foreach($voice_parts as $voice_part)
                                <label for="sender_voice_parts_{{ $voice_part->id }}" class="btn btn-outline-dark btn-check py-1 px-3 text-left d-flex align-items-center">
                                    <input id="sender_voice_parts_{{ $voice_part->id }}" name="sender_voice_parts[]" value="{{ $voice_part->id }}" type="checkbox" autocomplete="off">
                                    <span>{{ $voice_part->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sender_singer_categories">Singer Category</label>

                        <div class="btn-group btn-group-toggle d-flex bg-white" data-toggle="buttons">
                        @foreach($singer_categories as $category_id => $category_name)
                            <label for="sender_singer_categories_{{ $category_id }}" class="btn btn-outline-dark btn-check py-1 px-3 text-left d-flex align-items-center">
                                <input id="sender_singer_categories_{{ $category_id }}" name="sender_singer_categories[]" value="{{ $category_id }}" type="checkbox" autocomplete="off">
                                <span>{{ $category_name }}</span>
                            </label>
                        @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="mb-5">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-fw fa-check"></i> Create
        </button>
        <a href="{{ route('groups.index') }}" class="btn btn-link text-danger">
            <i class="fa fa-fw fa-times"></i> Cancel
        </a>
    </div>



    {{ Form::close() }}

@endsection