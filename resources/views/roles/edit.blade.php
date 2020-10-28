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
						{{ Form::label('name', 'Name') }}
						{{ Form::text('name', $role->name, ['class' => 'form-control']) }}
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
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="singers_all">
								<label class="custom-control-label" for="singers_all">Singers</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_view" name="abilities[]" value="singers_view" @if( in_array('singers_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singers_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_create" name="abilities[]" value="singers_create" @if( in_array('singers_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singers_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_update" name="abilities[]" value="singers_update" @if( in_array('singers_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singers_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_delete" name="abilities[]" value="singers_delete" @if( in_array('singers_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singers_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="singer_profiles_all">
								<label class="custom-control-label" for="singer_profiles_all">Singer Profiles</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_profiles_view" name="abilities[]" value="singer_profiles_view" @if( in_array('singer_profiles_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_profiles_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_profiles_create" name="abilities[]" value="singer_profiles_create" @if( in_array('singer_profiles_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_profiles_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_profiles_update" name="abilities[]" value="singer_profiles_update" @if( in_array('singer_profiles_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_profiles_update">Update</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="singer_placements_all">
								<label class="custom-control-label" for="singer_placements_all">Singer Placements</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_placements_view" name="abilities[]" value="singer_placements_view" @if( in_array('singer_placements_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_placements_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_placements_create" name="abilities[]" value="singer_placements_create" @if( in_array('singer_placements_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_placements_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_placements_update" name="abilities[]" value="singer_placements_update" @if( in_array('singer_placements_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="singer_placements_update">Update</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="voice_parts_all">
								<label class="custom-control-label" for="voice_parts_all">Voice Parts</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_view" name="abilities[]" value="voice_parts_view" @if( in_array('voice_parts_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="voice_parts_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_create" name="abilities[]" value="voice_parts_create" @if( in_array('voice_parts_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="voice_parts_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_update" name="abilities[]" value="voice_parts_update" @if( in_array('voice_parts_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="voice_parts_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_delete" name="abilities[]" value="voice_parts_delete" @if( in_array('voice_parts_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="voice_parts_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="roles_all">
								<label class="custom-control-label" for="roles_all">Roles</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_view" name="abilities[]" value="roles_view" @if( in_array('roles_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="roles_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_create" name="abilities[]" value="roles_create" @if( in_array('roles_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="roles_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_update" name="abilities[]" value="roles_update" @if( in_array('roles_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="roles_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_delete" name="abilities[]" value="roles_delete" @if( in_array('roles_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="roles_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Songs Module</th>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="songs_all">
								<label class="custom-control-label" for="songs_all">Songs</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_view" name="abilities[]" value="songs_view" @if( in_array('songs_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="songs_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_create" name="abilities[]" value="songs_create" @if( in_array('songs_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="songs_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_update" name="abilities[]" value="songs_update" @if( in_array('songs_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="songs_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_delete" name="abilities[]" value="songs_delete" @if( in_array('songs_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="songs_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Events Module</th>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="events_all">
								<label class="custom-control-label" for="events_all">Events</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_view" name="abilities[]" value="events_view" @if( in_array('events_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="events_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_create" name="abilities[]" value="events_create" @if( in_array('events_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="events_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_update" name="abilities[]" value="events_update" @if( in_array('events_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="events_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_delete" name="abilities[]" value="events_delete" @if( in_array('events_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="events_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="attendances_all">
								<label class="custom-control-label" for="attendances_all">Attendances</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_view" name="abilities[]" value="attendances_view" @if( in_array('attendances_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="attendances_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_create" name="abilities[]" value="attendances_create" @if( in_array('attendances_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="attendances_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_update" name="abilities[]" value="attendances_update" @if( in_array('attendances_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="attendances_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_delete" name="abilities[]" value="attendances_delete" @if( in_array('attendances_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="attendances_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="rsvps_all">
								<label class="custom-control-label" for="rsvps_all">RSVPs</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="rsvps_view" name="abilities[]" value="rsvps_view" @if( in_array('rsvps_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="rsvps_view">View</label>
							</div>
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
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="folders_all">
								<label class="custom-control-label" for="folders_all">Folders</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_view" name="abilities[]" value="folders_view" @if( in_array('folders_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="folders_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_create" name="abilities[]" value="folders_create" @if( in_array('folders_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="folders_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_update" name="abilities[]" value="folders_update" @if( in_array('folders_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="folders_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_delete" name="abilities[]" value="folders_delete" @if( in_array('folders_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="folders_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="documents_all">
								<label class="custom-control-label" for="documents_all">Documents</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="documents_view" name="abilities[]" value="documents_view" @if( in_array('documents_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="documents_view">Download</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="documents_create" name="abilities[]" value="documents_create" @if( in_array('documents_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="documents_create">Create</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="documents_delete" name="abilities[]" value="documents_delete" @if( in_array('documents_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="documents_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Riser Stacks Module</th>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="riser_stacks_all">
								<label class="custom-control-label" for="riser_stacks_all">Riser Stacks</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_view" name="abilities[]" value="riser_stacks_view" @if( in_array('riser_stacks_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="riser_stacks_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_create" name="abilities[]" value="riser_stacks_create" @if( in_array('riser_stacks_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="riser_stacks_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_update" name="abilities[]" value="riser_stacks_update" @if( in_array('riser_stacks_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="riser_stacks_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_delete" name="abilities[]" value="riser_stacks_delete" @if( in_array('riser_stacks_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="riser_stacks_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Mailing Lists Module</th>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="mailing_lists_all">
								<label class="custom-control-label" for="mailing_lists_all">Mailing Lists</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_view" name="abilities[]" value="mailing_lists_view" @if( in_array('mailing_lists_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="mailing_lists_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_create" name="abilities[]" value="mailing_lists_create" @if( in_array('mailing_lists_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="mailing_lists_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_update" name="abilities[]" value="mailing_lists_update" @if( in_array('mailing_lists_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="mailing_lists_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_delete" name="abilities[]" value="mailing_lists_delete" @if( in_array('mailing_lists_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="mailing_lists_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr class="table-light">
						<th colspan="100">Onboarding Module</th>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="tasks_all">
								<label class="custom-control-label" for="tasks_all">Tasks</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="tasks_view" name="abilities[]" value="tasks_view" @if( in_array('tasks_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="tasks_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="tasks_create" name="abilities[]" value="tasks_create" @if( in_array('tasks_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="tasks_create">Create</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="tasks_delete" name="abilities[]" value="tasks_delete" @if( in_array('tasks_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="tasks_delete">Delete</label>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input check-all" id="notifications_all">
								<label class="custom-control-label" for="notifications_all">Task Notifications</label>
							</div>
						</th>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_view" name="abilities[]" value="notifications_view" @if( in_array('notifications_view', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="notifications_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_create" name="abilities[]" value="notifications_create" @if( in_array('notifications_create', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="notifications_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_update" name="abilities[]" value="notifications_update" @if( in_array('notifications_update', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="notifications_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_delete" name="abilities[]" value="notifications_delete" @if( in_array('notifications_delete', $role->abilities, true) ) checked @endif>
								<label class="custom-control-label" for="notifications_delete">Delete</label>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-fw fa-check"></i> Save
				</button>
				<a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
					<i class="fa fa-fw fa-times"></i> Cancel
				</a>
			</div>
		</div>

	</div>
</div>


{{ Form::close() }}

@endsection