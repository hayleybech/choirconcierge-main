@extends('layouts.page')

@section('title', 'Add Task')
@section('page-title', 'Add Task')

@section('page-content')

    {{ Form::open( [ 'route' => 'tasks.store', 'method' => 'post' ] ) }}

    <div class="card">
        <h3 class="card-header h4">Tasks Details</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('name', 'Task Name') }}
                {{ Form::text('name', '', ['class' => 'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('role_id', 'Role') }}
                {{ Form::select('role_id',
                    $roles_keyed,
                    '',
                    ['required', 'class' => 'custom-select']
                ) }}
            </div>

            <div class="form-group">
                {{ Form::label('type', 'Type') }}
                {{ Form::text('type', 'manual', ['class' => 'form-control-plaintext', 'readonly']) }}
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-check"></i> Create
            </button>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-fw fa-times"></i> Cancel
            </a>
        </div>

    </div>

    {{ Form::close() }}

@endsection