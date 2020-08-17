<table class="table card-table">
    <thead>
    <tr class="row--event">
        <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--type"><a href="{{ $sorts['type.title']['url'] }}">Type<i class="fa fas sort-{{ $sorts['type.title']['dir'] }} {{ ($sorts['type.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--date">Event Date</th>
        <th class="col--location">Location</th>
        <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--delete"></th>
    </tr>
    </thead>
    <tbody>
    @each('events.index_row', $events, 'event', 'partials.noresults-table')
    </tbody>
</table>

<div class="card-footer">
    {{ $events->count() }} events
</div>