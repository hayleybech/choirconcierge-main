<table class="table card-table">
    <thead>
    <tr class="row--event">
        <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="ml-1 fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="ml-1 fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--date">Event Date</th>
        <th class="col--location">Location</th>
        @if($col_attendance)
            @can('viewAny', \App\Models\Attendance::class)
            <th class="col--attendance">Attendance</th>
            @endcan
        @endif
        <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="ml-1 fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--delete"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($events as $event)
        @include('events.index_row', ['event' => $event, 'col_attendance' => $col_attendance])
    @empty
        @include('partials.noresults-table')
    @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="100">{{ $events->count() }} events</td>
        </tr>
    </tfoot>
</table>