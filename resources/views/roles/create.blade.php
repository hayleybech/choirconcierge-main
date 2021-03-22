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
							<x-inputs.checkbox label="Singers" id="singers_all" name="singers_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="singers_view" name="abilities[]" value="singers_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="singers_create" name="abilities[]" value="singers_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="singers_update" name="abilities[]" value="singers_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="singers_delete" name="abilities[]" value="singers_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Singer Profiles" id="singer_profiles_all" name="singer_profiles_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="singer_profiles_view" name="abilities[]" value="singer_profiles_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="singer_profiles_create" name="abilities[]" value="singer_profiles_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="singer_profiles_update" name="abilities[]" value="singer_profiles_update"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Singer Placements" id="singer_placements_all" name="singer_placements_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="singer_placements_view" name="abilities[]" value="singer_placements_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="singer_placements_create" name="abilities[]" value="singer_placements_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="singer_placements_update" name="abilities[]" value="singer_placements_update"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="voice_parts_view" name="abilities[]" value="voice_parts_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="voice_parts_create" name="abilities[]" value="voice_parts_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="voice_parts_update" name="abilities[]" value="voice_parts_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="voice_parts_delete" name="abilities[]" value="voice_parts_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Roles" id="roles_all" name="roles_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="roles_view" name="abilities[]" value="roles_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="roles_create" name="abilities[]" value="roles_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="roles_update" name="abilities[]" value="roles_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="roles_delete" name="abilities[]" value="roles_delete"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="songs_view" name="abilities[]" value="songs_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="songs_create" name="abilities[]" value="songs_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="songs_update" name="abilities[]" value="songs_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="songs_delete" name="abilities[]" value="songs_delete"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="events_view" name="abilities[]" value="events_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="events_create" name="abilities[]" value="events_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="events_update" name="abilities[]" value="events_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="events_delete" name="abilities[]" value="events_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Attendances" id="attendances_all" name="attendances_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="attendances_view" name="abilities[]" value="attendances_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="attendances_create" name="abilities[]" value="attendances_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="attendances_update" name="abilities[]" value="attendances_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="attendances_delete" name="abilities[]" value="attendances_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="RSVPs" id="rsvps_all" name="rsvps_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="rsvps_view" name="abilities[]" value="rsvps_view"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="folders_view" name="abilities[]" value="folders_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="folders_create" name="abilities[]" value="folders_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="folders_update" name="abilities[]" value="folders_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="folders_delete" name="abilities[]" value="folders_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Documents" id="documents_all" name="documents_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="documents_view" name="abilities[]" value="documents_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="documents_create" name="abilities[]" value="documents_create"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="documents_delete" name="abilities[]" value="documents_delete"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="riser_stacks_view" name="abilities[]" value="riser_stacks_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="riser_stacks_create" name="abilities[]" value="riser_stacks_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="riser_stacks_update" name="abilities[]" value="riser_stacks_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="riser_stacks_delete" name="abilities[]" value="riser_stacks_delete"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="mailing_lists_view" name="abilities[]" value="mailing_lists_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="mailing_lists_create" name="abilities[]" value="mailing_lists_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="mailing_lists_update" name="abilities[]" value="mailing_lists_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="mailing_lists_delete" name="abilities[]" value="mailing_lists_delete"></x-inputs.checkbox>
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
							<x-inputs.checkbox label="View" id="tasks_view" name="abilities[]" value="tasks_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="tasks_create" name="abilities[]" value="tasks_create"></x-inputs.checkbox>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="tasks_delete" name="abilities[]" value="tasks_delete"></x-inputs.checkbox>
						</td>
					</tr>
					<tr>
						<th>
							<x-inputs.checkbox label="Task Notifications" id="notifications_all" name="notifications_all" value="true" class="check-all"></x-inputs.checkbox>
						</th>
						<td>
							<x-inputs.checkbox label="View" id="notifications_view" name="abilities[]" value="notifications_view"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Create" id="notifications_create" name="abilities[]" value="notifications_create"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Update" id="notifications_update" name="abilities[]" value="notifications_update"></x-inputs.checkbox>
						</td>
						<td>
							<x-inputs.checkbox label="Delete" id="notifications_delete" name="abilities[]" value="notifications_delete"></x-inputs.checkbox>
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