@extends('layouts.page')

@section('title', 'Voice Parts')
@section('page-title')
	<i class="fal fa-users-class fa-fw"></i> Voice Parts
	<a href="{{ route( 'voice-parts.create' ) }}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-plus"></i> Add New</a>
@endsection
@section('page-action')
@endsection

@section('page-lead', 'On this page you can tweak the names of your voice parts, and add/remove them when necessary. NOTE: Changing an existing part will affect singers in that part.')

@section('page-content')
	<div class="card">
		<h3 class="card-header h4">Voice Part List</h3>

		<div class="list-group-flush">
			@foreach($parts as $part)
				<div class="list-group-item d-flex justify-content-between">
					<a href="{{ route('voice-parts.show', $part) }}"><span class="mx-2 rounded" style="display: inline-block; width: 1em; height: 1em; border: 1px solid #ddd; background-color: {{ $part->colour }};"></span> {{ $part->title }}</a>
					<x-delete-button :action="route( 'voice-parts.destroy', $part )"/>
				</div>
			@endforeach
		</div>
	</div>
@endsection