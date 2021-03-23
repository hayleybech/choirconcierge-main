@extends('layouts.page')

@section('title', 'Add Notification')
@section('page-title', 'Add Notification')

@section('page-content')

    {{ Form::open( [ 'route' => ['tasks.notifications.store', $task], 'method' => 'post' ] ) }}

    <div class="row">
        <div class="col-md-12">

        <div class="card">
        <h3 class="card-header h4">Notification Details</h3>

        <div class="card-body">

            <div class="form-group">
                <x-inputs.text label="Subject" id="subject" name="subject" placeholder="Congrats for doing a thing!"></x-inputs.text>
            </div>

            <div class="form-group">
                <x-inputs.text label="Recipient(s)" id="recipients" name="recipients">
                    <x-slot name="helpText">e.g. The singer: <code>singer:0</code>, a user role: <code>role:1</code>, a specific user: <code>user:1</code>.</x-slot>
                </x-inputs.text>
            </div>

            <div class="form-group">
                {{ Form::label('body', 'Body') }}
                <div class="row">
                    <div class="col-9">
                        {{ Form::textarea('body', '', ['class' => 'form-control']) }}
                    </div>
                    <div class="col-3">
                        <small class="form-text text-muted"><strong>You can include any of the following snippets:</strong>
                            Singer full name: <code>%%singer.name%%</code>, <br>
                            Singer first name: <code>%%singer.fname%%</code>, <br>
                            Singer last name: <code>%%singer.lname%%</code>, <br>
                            Singer email: <code>%%singer.email%%</code>, <br>
                            Create profile link: <code>%%profile.create%%</code>, <br>
                            Create placement link: <code>%%placement.create%%</code>, <br>
                            Choir name: <code>%%choir.name%%</code>, <br>
                            Recipient full name: <code>%%user.name%%</code>, <br>
                            Recipient first name: <code>%%user.fname%%</code>, <br>
                            Recipient last name: <code>%%user.lname%%</code>, <br>
                            <br>
                            <strong>If the singer has a member profile: </strong><br>
                            Singer DOB: <code>%%singer.dob%%</code>, <br>
                            Singer age: <code>%%singer.age%%</code>, <br>
                            Singer phone: <code>%%singer.phone%%</code>, <br>
                            <br>
                            <strong>If the singer has a voice placement: </strong><br>
                            Voice part: <code>%%singer.section%%</code>, <br>
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <x-inputs.text label="Delay" id="delay" name="delay" placeholder="1 second" help-text="Try something like '4 hours' or '28 days'. "></x-inputs.text>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-check"></i> Create
            </button>
            <a href="{{ route('tasks.index') }}" class="btn btn-link text-danger">
                <i class="fa fa-fw fa-times"></i> Cancel
            </a>
        </div>

    </div>

        </div>
    </div>
    
    {{ Form::close() }}

@endsection