<tr>
	<th scope="row"><a class="item-title" href="{{route('tasks.show', $task) }}">{{ $task->name }}</a></th>
	<td>{{ $task->role->name }}</td>
	<td>{{ $task->type }} <small>({{ $task->route }})</small></td>
</tr>