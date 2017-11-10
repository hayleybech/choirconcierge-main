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
	</div
	@endif
		
	<div class="row">

		<ul>
			@foreach($users as $user)
			<li>
			{{$user->name}}: 
				@foreach($user->roles as $role){{ $loop->first ? '' : ', ' }} {{ $role->name }}@endforeach
			</li>
			@endforeach
		</ul>

	</div>
	
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