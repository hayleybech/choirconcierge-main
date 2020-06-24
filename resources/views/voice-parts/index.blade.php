@extends('layouts.page')

@section('title', 'Voice Parts')
@section('page-title')
<i class="fal fa-users-class fa-fw"></i> Voice Parts
@endsection
@section('page-action')
	<a href="{{ route( 'voice-parts.create' ) }}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-plus"></i> Add New</a>
@endsection

@section('page-lead', 'On this page you can tweak the names of your voice parts, and add/remove them when necessary. NOTE: Changing an existing part will affect singers in that part.')

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h4">Voice Part List</h3>

		<div class="list-group-flush">
			@foreach($parts as $part)
				<div class="list-group-item d-flex justify-content-between">
					<a href="{{ route('voice-parts.show', $part) }}">{{ $part->title }}</a>
					<x-delete-button :action="route( 'voice-parts.destroy', $part )"/>
				</div>
			@endforeach
		</div>
	</div>

@endsection