<tr class="row--event">
    <td class="col--title">
        <a class="item-title" href="{{route('stacks.show', ['stack' => $stack])}}">
        {{ ( isset($stack->title) ) ? $stack->title : 'Title Unknown' }}
        </a>
    </td>
    <td class="col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $stack->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $stack->created_at->format(config('app.formats.timestamp_md')) }}
            </div>
        </div>
    </td>
    <td class="col--delete">
        @can('delete', $stack)
            <x-delete-button :action="route( 'stacks.destroy', ['stack' => $stack] )"/>
        @endcan
    </td>

</tr>

