@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

	<h2 class="display-4">{{$singer->name}}</h2>
	
	<p class="mb-2 text-muted singer-email">
		Email: {{ $singer->email }}<br>
		Added: {{$singer->created_at}}
	</p>
	
	<div class="card">
		<div class="card-header">
			<h3 class="h6">Member Profile</h3>
		</div>
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong>DOB:</strong> {{$singer->profile->dob}}
			</li>
			<li class="list-group-item">
				<strong>Phone:</strong> {{$singer->profile->phone}}
			</li>
			<li class="list-group-item">
				<strong>Emergency Contact Name:</strong> {{$singer->profile->ice_name}}
			</li>
			<li class="list-group-item">
				<strong>Emergency Contact Phone:</strong> {{$singer->profile->ice_phone}}
			</li>
			<li class="list-group-item">
				<strong>Reason for joining:</strong> {{$singer->profile->reason_for_joining}}
			</li>
			<li class="list-group-item">
				<strong>Referred by:</strong> {{$singer->profile->referrer}}
			</li>
			<li class="list-group-item">
				<strong>Profession:</strong> {{$singer->profile->profession}}
			</li>
			<li class="list-group-item">
				<strong>Other Skills:</strong> {{$singer->profile->skills}}
			</li>
		</ul>
	</div>
	

@endsection