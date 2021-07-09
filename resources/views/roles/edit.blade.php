@extends('layouts.page')

@section('title', 'Edit - ' . $role->name)
@section('page-title', 'Edit - ' . $role->name)

@section('page-content')

{{ Form::open( [ 'route' => ['roles.update', $role], 'method' => 'put' ] ) }}

<div class="row">
	<div class="col-md-12">

		<div class="card">
			<div class="card-header"><h3 class="h4">Role Details</h3></div>

				@if($role->name === 'User')
					{{ Form::hidden('name', $role->name, ) }}
				@else
				<div class="card-body">
					<p>
						<x-inputs.text label="Name" id="name" name="name" value="{{ $role->name }}"></x-inputs.text>
					</p>
				</div>
				@endif

			<div class="table-responsive">
				<table id="role-abilities-table" class="table card-table">
					<thead class="thead-light">
					<tr>
						<th>Role</th>
						<th>View</th>
						<th>Create</th>
						<th>Update</th>
						<th>Delete</th>
					</tr>
					</thead>
					<tbody>
					<tr class="table-light">
						<th colspan="100">
							Singers Module
						</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Singers" id="singers_all" name="singers_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="singers_view" name="abilities[]" value="singers_view" :checked="in_array('singers_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="singers_create" name="abilities[]" value="singers_create" :checked="in_array('singers_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="singers_update" name="abilities[]" value="singers_update" :checked="in_array('singers_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="singers_delete" name="abilities[]" value="singers_delete" :checked="in_array('singers_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Singer Placements" id="singer_placements_all" name="singer_placements_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="singer_placements_view" name="abilities[]" value="singer_placements_view" :checked="in_array('singer_placements_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="singer_placements_create" name="abilities[]" value="singer_placements_create" :checked="in_array('singer_placements_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="singer_placements_update" name="abilities[]" value="singer_placements_update" :checked="in_array('singer_placements_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Voice Parts" id="voice_parts_all" name="voice_parts_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="voice_parts_view" name="abilities[]" value="voice_parts_view" :checked="in_array('voice_parts_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="voice_parts_create" name="abilities[]" value="voice_parts_create" :checked="in_array('voice_parts_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="voice_parts_update" name="abilities[]" value="voice_parts_update" :checked="in_array('voice_parts_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="voice_parts_delete" name="abilities[]" value="voice_parts_delete" :checked="in_array('voice_parts_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Roles" id="roles_all" name="roles_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="roles_view" name="abilities[]" value="roles_view" :checked="in_array('roles_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="roles_create" name="abilities[]" value="roles_create" :checked="in_array('roles_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="roles_update" name="abilities[]" value="roles_update" :checked="in_array('roles_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="roles_delete" name="abilities[]" value="roles_delete" :checked="in_array('roles_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Songs Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Songs" id="songs_all" name="songs_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="songs_view" name="abilities[]" value="songs_view" :checked="in_array('songs_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="songs_create" name="abilities[]" value="songs_create" :checked="in_array('songs_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="songs_update" name="abilities[]" value="songs_update" :checked="in_array('songs_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="songs_delete" name="abilities[]" value="songs_delete" :checked="in_array('songs_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Events Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Events" id="events_all" name="events_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="events_view" name="abilities[]" value="events_view" :checked="in_array('events_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="events_create" name="abilities[]" value="events_create" :checked="in_array('events_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="events_update" name="abilities[]" value="events_update" :checked="in_array('events_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="events_delete" name="abilities[]" value="events_delete" :checked="in_array('events_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Attendances" id="attendances_all" name="attendances_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="attendances_view" name="abilities[]" value="attendances_view" :checked="in_array('attendances_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="attendances_create" name="abilities[]" value="attendances_create" :checked="in_array('attendances_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="attendances_update" name="abilities[]" value="attendances_update" :checked="in_array('attendances_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="attendances_delete" name="abilities[]" value="attendances_delete" :checked="in_array('attendances_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="RSVPs" id="rsvps_all" name="rsvps_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="rsvps_view" name="abilities[]" value="rsvps_view" :checked="in_array('rsvps_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Documents Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Folders" id="folders_all" name="folders_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="folders_view" name="abilities[]" value="folders_view" :checked="in_array('folders_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="folders_create" name="abilities[]" value="folders_create" :checked="in_array('folders_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="folders_update" name="abilities[]" value="folders_update" :checked="in_array('folders_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="folders_delete" name="abilities[]" value="folders_delete" :checked="in_array('folders_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Documents" id="documents_all" name="documents_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="documents_view" name="abilities[]" value="documents_view" :checked="in_array('documents_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="documents_create" name="abilities[]" value="documents_create" :checked="in_array('documents_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="documents_delete" name="abilities[]" value="documents_delete" :checked="in_array('documents_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Riser Stacks Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Riser Stacks" id="riser_stacks_all" name="riser_stacks_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="riser_stacks_view" name="abilities[]" value="riser_stacks_view" :checked="in_array('riser_stacks_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="riser_stacks_create" name="abilities[]" value="riser_stacks_create" :checked="in_array('riser_stacks_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="riser_stacks_update" name="abilities[]" value="riser_stacks_update" :checked="in_array('riser_stacks_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="riser_stacks_delete" name="abilities[]" value="riser_stacks_delete" :checked="in_array('riser_stacks_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Mailing Lists Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Mailing Lists" id="mailing_lists_all" name="mailing_lists_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="mailing_lists_view" name="abilities[]" value="mailing_lists_view" :checked="in_array('mailing_lists_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="mailing_lists_create" name="abilities[]" value="mailing_lists_create" :checked="in_array('mailing_lists_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="mailing_lists_update" name="abilities[]" value="mailing_lists_update" :checked="in_array('mailing_lists_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="mailing_lists_delete" name="abilities[]" value="mailing_lists_delete" :checked="in_array('mailing_lists_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Onboarding Module</th>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Tasks" id="tasks_all" name="tasks_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="tasks_view" name="abilities[]" value="tasks_view" :checked="in_array('tasks_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="tasks_create" name="abilities[]" value="tasks_create" :checked="in_array('tasks_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="tasks_delete" name="abilities[]" value="tasks_delete" :checked="in_array('tasks_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Task Notifications" id="notifications_all" name="notifications_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="notifications_view" name="abilities[]" value="notifications_view" :checked="in_array('notifications_view', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="notifications_create" name="abilities[]" value="notifications_create" :checked="in_array('notifications_create', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="notifications_update" name="abilities[]" value="notifications_update" :checked="in_array('notifications_update', $role->abilities, true)"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="notifications_delete" name="abilities[]" value="notifications_delete" :checked="in_array('notifications_delete', $role->abilities, true)"></x-inputs.checkbox>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-fw fa-check"></i> Save
				</button>
				<a href="{{ route('roles.index') }}" class="btn btn-link text-danger">
					<i class="fa fa-fw fa-times"></i> Cancel
				</a>
			</div>
		</div>

	</div>
</div>


{{ Form::close() }}

@endsection