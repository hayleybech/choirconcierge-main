@extends('layouts.app')

@section('title', 'Users')

@section('content')

	<h2>Users</h2>

	@if (session('status'))
	<div class="alert {{ isset($Response->error) ? 'alert-danger' : 'alert-success' }}" role="alert">
		{{ session('status') }}
		
		@isset( $Response->error )
		<pre>
			{{ var_dump($Response) }} 
			@json($args)
		</pre>
		@endisset
	</div>
	@endif
		
	@foreach($users as $user)
	<div class="card">
		<div class="card-header">{{$user->name}}</div>
		<ul class="list-group list-group-flush">
			@foreach($user->roles as $role)
			<li class="list-group-item">{{ $role->name }}</li>
			@endforeach
		  </ul>
		<div class="card-body">
		<form>
			<div class="row no-gutters">
				<div class="col-10">
					<select class="form-control" id="input_role" name="role">
						<option value="" selected disabled hidden>Choose Role</option>
						<option>Admin</option>
						<option>Membership Team</option>
						<option>Music Team</option>
						<option>Accounts Team</option>
						<option>Uniforms Team</option>
					</select>
				</div>
				<div class="col-2">
					<input class="btn btn-default" type="submit" value="Add">
				</div>
			</div>
		</form>
		</div>
	</div>
	@endforeach
	
	<form>
		<h3>Add User</h3>
		Email <input type="email" name="email">
		Name <input type="text" name="name">
		Role 
		<select>
			<option>Admin</option>
			<option>Music Team</option>
		</select>
	</form>

@endsection