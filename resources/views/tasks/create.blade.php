@extends('layouts.page')

@section('title', 'Add Task')
@section('page-title', 'Add Task')

@section('page-content')

    {{ Form::open( [ 'route' => 'tasks.store', 'method' => 'post' ] ) }}

    <div class="card">
        <h3 class="card-header h4">Tasks Details</h3>

        <div class="card-body">

            <div class="form-group">
                <x-inputs.text label="Task Name" id="name" name="name"></x-inputs.text>
            </div>

            <div class="form-group">
                <x-inputs.select label="Role" id="role_id" name="role_id" :options="$roles_keyed"></x-inputs.select>
            </div>

            <div class="form-group">
                <x-inputs.text label="Type" id="type" name="type" value="manual" readonly plain="true"></x-inputs.text>
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

    {{ Form::close() }}

@endsection