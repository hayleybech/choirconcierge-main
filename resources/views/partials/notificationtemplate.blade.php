<tr>
	<th scope="row">{{ $template->task->name }}</th>
	<td>{{ $template->subject }}</td>
	<td>{{ $template->recipients }}</td>
	<td>{!! $template->body_rendered !!}</td>
	<td>{{ $template->delay }}</td>
</tr>