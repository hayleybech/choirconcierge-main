<tr>
	<th scope="row"><a class="item-title" href="{{route('tasks.show', $task) }}">{{ $task->name }}</a></th>
	<td>{{ $task->role->name }}</td>
	<td>{{ $task->type }} <small>({{ $task->route }})</small></td>
	<td>
	@can('delete', $task)
		<x-delete-button :action="route( 'tasks.destroy', $task )" :message="'This may break your onboarding process!'"/>
	@endif
	</td>
</tr>