@extends('layouts.page')

@section('title', $role->name . ' - Roles')
@section('page-title', $role->name)
@section('page-action')
    @can('update', $role)
	<a href="{{ route( 'roles.edit', ['role' => $role] ) }}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endcan
@endsection
@section('page-lead')
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
                    Singers
                </th>
                <td>
                    @if( in_array('singers_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('singers_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('singers_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('singers_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    Singer Profiles
                </th>
                <td>
                    @if( in_array('singer_profiles_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('singer_profiles_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('singer_profiles_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <th>
                    Singer Placements
                </th>
                <td>
                    @if( in_array('singer_placements_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('singer_placements_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('singer_placements_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <th>
                    Voice Parts
                </th>
                <td>
                    @if( in_array('voice_parts_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('voice_parts_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('voice_parts_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('voice_parts_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    Roles
                </th>
                <td>
                    @if( in_array('roles_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('roles_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('roles_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('roles_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr class="table-light">
                <th colspan="100">Songs Module</th>
            </tr>
            <tr>
                <th>
                    Songs
                </th>
                <td>
                    @if( in_array('songs_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('songs_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('songs_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('songs_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr class="table-light">
                <th colspan="100">Events Module</th>
            </tr>
            <tr>
                <th>
                    Events
                </th>
                <td>
                    @if( in_array('events_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('events_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('events_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('events_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    Attendances
                </th>
                <td>
                    @if( in_array('attendances_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('attendances_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('attendances_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('attendances_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    RSVPs
                </th>
                <td>
                    @if( in_array('rsvps_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
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
                    Folders
                </th>
                <td>
                    @if( in_array('folders_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('folders_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('folders_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('folders_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    Documents
                </th>
                <td>
                    @if( in_array('documents_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('documents_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    @if( in_array('documents_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr class="table-light">
                <th colspan="100">Riser Stacks Module</th>
            </tr>
            <tr>
                <th>
                    Riser Stacks
                </th>
                <td>
                    @if( in_array('riser_stacks_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('riser_stacks_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('riser_stacks_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('riser_stacks_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr class="table-light">
                <th colspan="100">Mailing Lists Module</th>
            </tr>
            <tr>
                <th>
                    Mailing Lists
                </th>
                <td>
                    @if( in_array('mailing_lists_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View
                </td>
                <td>
                    @if( in_array('mailing_lists_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('mailing_lists_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('mailing_lists_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr class="table-light">
                <th colspan="100">Onboarding Module</th>
            </tr>
            <tr>
                <th>
                    Tasks
                </th>
                <td>
                    @if( in_array('tasks_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View

                </td>
                <td>
                    @if( in_array('tasks_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    &nbsp;
                </td>
                <td>
                    @if( in_array('tasks_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            <tr>
                <th>
                    Task Notifications
                </th>
                <td>
                    @if( in_array('notifications_view', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    View

                </td>
                <td>
                    @if( in_array('notifications_create', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Create
                </td>
                <td>
                    @if( in_array('notifications_update', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Update
                </td>
                <td>
                    @if( in_array('notifications_delete', $role->abilities, true) )
                        <i class="far fa-fw fa-check mr-2"></i>
                    @else
                        <i class="far fa-fw fa-times mr-2"></i>
                    @endif
                    Delete
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('page-content')


@endsection