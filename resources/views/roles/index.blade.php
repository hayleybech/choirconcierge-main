@extends('layouts.page')

@section('title', 'Roles')
@section('page-title')
	<i class="fal fa-user-tag fa-fw"></i> Roles
	<a href="{{ route( 'roles.create' ) }}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-plus"></i> Add New</a>
@endsection
@section('page-action')
@endsection

@section('page-lead', 'On this page you can manage your team\'s roles and manage their abilities. ')

@section('page-content')
	<div class="card">
		<h3 class="card-header h4">Role List</h3>

		<div class="list-group-flush">
		@foreach($roles as $role)
			<div class="list-group-item d-flex justify-content-between">
				<a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a>
				@can('delete', $role)
				<x-delete-button :action="route( 'roles.destroy', $role )"/>
				@endcan
			</div>
		@endforeach
		</div>
	</div>
@endsection