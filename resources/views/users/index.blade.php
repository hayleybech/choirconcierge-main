@extends('layouts.page')

@section('title', 'Users')
@section('page-title', 'Users')

@section('page-content')
	
	<div id="accordion" class="accordion">
	@foreach($users as $key => $user)
	<div class="card bg-light">
		<div class="card-header"><a class="btn btn-link collapsed" data-toggle="collapse" href="#collapse-{{$key}}" aria-expanded="false" aria-controls="collapse-{{$key}}">{{$user->name}}</a></div>
		<div class="collapse" id="collapse-{{$key}}" data-parent="#accordion" >
			<ul class="list-group list-group-flush">
				@foreach($user->roles as $role)
				<li class="list-group-item d-flex justify-content-between align-items-center">
					{{ $role->name }}
					<form method="get" action="/users/{{$user->id}}/roles/{{$role->id}}/detach">
						<button type="submit" class="btn btn-link text-danger btn-sm"><i class="fa fa-times"></i></button>
					</form>
				</li>
				@endforeach
			  </ul>
			<div class="card-body">
			<form method="post" action="/users/{{$user->id}}/role">
				{{ csrf_field() }}
				<div class="row no-gutters">
					<div class="col-10">
						<select class="form-control" id="input_role" name="roles[]">
							<option value="" selected disabled hidden>Choose Role</option>
							@foreach( $roles_all as $role )
								@if( ! $user->hasRole( $role->name ) )
								<option value="{{$role->id}}">{{$role->name}}</option>
								@endif
							@endforeach
						</select>
					</div>
					<div class="col-2">
						<input class="btn btn-default" type="submit" value="Add">
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
	@endforeach
	</div>
	
	<!--
	<form>
		<h3>Add User</h3>
		Email <input type="email" name="email">
		Name <input type="text" name="name">
		Role 
		<select>
			<option>Admin</option>
			<option>Music Team</option>
		</select>
	</form>-->

@endsection