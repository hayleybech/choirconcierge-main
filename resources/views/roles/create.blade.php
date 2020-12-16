@extends('layouts.page')

@section('title', 'Add Role')
@section('page-title', 'Add Role')

@section('page-content')

{{ Form::open( [ 'route' => 'roles.store', 'method' => 'post' ] ) }}

<div class="row">
	<div class="col-md-12">

		<div class="card">
			<div class="card-header"><h3 class="h4">Role Details</h3></div>

			<div class="card-body">
				<p>
					{{ Form::label('name', 'Name') }}
					{{ Form::text('name', '', ['class' => 'form-control']) }}
				</p>
			</div>

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
								<input type="checkbox" class="custom-control-input" id="singers_view" name="abilities[]" value="singers_view">
								<label class="custom-control-label" for="singers_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_create" name="abilities[]" value="singers_create">
								<label class="custom-control-label" for="singers_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_update" name="abilities[]" value="singers_update">
								<label class="custom-control-label" for="singers_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singers_delete" name="abilities[]" value="singers_delete">
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
								<input type="checkbox" class="custom-control-input" id="singer_profiles_view" name="abilities[]" value="singer_profiles_view">
								<label class="custom-control-label" for="singer_profiles_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_profiles_create" name="abilities[]" value="singer_profiles_create">
								<label class="custom-control-label" for="singer_profiles_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_profiles_update" name="abilities[]" value="singer_profiles_update">
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
								<input type="checkbox" class="custom-control-input" id="singer_placements_view" name="abilities[]" value="singer_placements_view">
								<label class="custom-control-label" for="singer_placements_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_placements_create" name="abilities[]" value="singer_placements_create">
								<label class="custom-control-label" for="singer_placements_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="singer_placements_update" name="abilities[]" value="singer_placements_update">
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
								<input type="checkbox" class="custom-control-input" id="voice_parts_view" name="abilities[]" value="voice_parts_view">
								<label class="custom-control-label" for="voice_parts_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_create" name="abilities[]" value="voice_parts_create">
								<label class="custom-control-label" for="voice_parts_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_update" name="abilities[]" value="voice_parts_update">
								<label class="custom-control-label" for="voice_parts_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="voice_parts_delete" name="abilities[]" value="voice_parts_delete">
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
								<input type="checkbox" class="custom-control-input" id="roles_view" name="abilities[]" value="roles_view">
								<label class="custom-control-label" for="roles_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_create" name="abilities[]" value="roles_create">
								<label class="custom-control-label" for="roles_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_update" name="abilities[]" value="roles_update">
								<label class="custom-control-label" for="roles_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="roles_delete" name="abilities[]" value="roles_delete">
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
								<input type="checkbox" class="custom-control-input" id="songs_view" name="abilities[]" value="songs_view">
								<label class="custom-control-label" for="songs_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_create" name="abilities[]" value="songs_create">
								<label class="custom-control-label" for="songs_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_update" name="abilities[]" value="songs_update">
								<label class="custom-control-label" for="songs_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="songs_delete" name="abilities[]" value="songs_delete">
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
								<input type="checkbox" class="custom-control-input" id="events_view" name="abilities[]" value="events_view">
								<label class="custom-control-label" for="events_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_create" name="abilities[]" value="events_create">
								<label class="custom-control-label" for="events_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_update" name="abilities[]" value="events_update">
								<label class="custom-control-label" for="events_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="events_delete" name="abilities[]" value="events_delete">
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
								<input type="checkbox" class="custom-control-input" id="attendances_view" name="abilities[]" value="attendances_view">
								<label class="custom-control-label" for="attendances_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_create" name="abilities[]" value="attendances_create">
								<label class="custom-control-label" for="attendances_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_update" name="abilities[]" value="attendances_update">
								<label class="custom-control-label" for="attendances_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="attendances_delete" name="abilities[]" value="attendances_delete">
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
								<input type="checkbox" class="custom-control-input" id="rsvps_view" name="abilities[]" value="rsvps_view">
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
								<input type="checkbox" class="custom-control-input" id="folders_view" name="abilities[]" value="folders_view">
								<label class="custom-control-label" for="folders_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_create" name="abilities[]" value="folders_create">
								<label class="custom-control-label" for="folders_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_update" name="abilities[]" value="folders_update">
								<label class="custom-control-label" for="folders_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="folders_delete" name="abilities[]" value="folders_delete">
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
								<input type="checkbox" class="custom-control-input" id="documents_view" name="abilities[]" value="documents_view">
								<label class="custom-control-label" for="documents_view">Download</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="documents_create" name="abilities[]" value="documents_create">
								<label class="custom-control-label" for="documents_create">Create</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="documents_delete" name="abilities[]" value="documents_delete">
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
								<input type="checkbox" class="custom-control-input" id="riser_stacks_view" name="abilities[]" value="riser_stacks_view">
								<label class="custom-control-label" for="riser_stacks_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_create" name="abilities[]" value="riser_stacks_create">
								<label class="custom-control-label" for="riser_stacks_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_update" name="abilities[]" value="riser_stacks_update">
								<label class="custom-control-label" for="riser_stacks_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="riser_stacks_delete" name="abilities[]" value="riser_stacks_delete">
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
								<input type="checkbox" class="custom-control-input" id="mailing_lists_view" name="abilities[]" value="mailing_lists_view">
								<label class="custom-control-label" for="mailing_lists_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_create" name="abilities[]" value="mailing_lists_create">
								<label class="custom-control-label" for="mailing_lists_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_update" name="abilities[]" value="mailing_lists_update">
								<label class="custom-control-label" for="mailing_lists_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="mailing_lists_delete" name="abilities[]" value="mailing_lists_delete">
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
								<input type="checkbox" class="custom-control-input" id="tasks_view" name="abilities[]" value="tasks_view">
								<label class="custom-control-label" for="tasks_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="tasks_create" name="abilities[]" value="tasks_create">
								<label class="custom-control-label" for="tasks_create">Create</label>
							</div>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="tasks_delete" name="abilities[]" value="tasks_delete">
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
								<input type="checkbox" class="custom-control-input" id="notifications_view" name="abilities[]" value="notifications_view">
								<label class="custom-control-label" for="notifications_view">View</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_create" name="abilities[]" value="notifications_create">
								<label class="custom-control-label" for="notifications_create">Create</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_update" name="abilities[]" value="notifications_update">
								<label class="custom-control-label" for="notifications_update">Update</label>
							</div>
						</td>
						<td>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="notifications_delete" name="abilities[]" value="notifications_delete">
								<label class="custom-control-label" for="notifications_delete">Delete</label>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-fw fa-check"></i> Create
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