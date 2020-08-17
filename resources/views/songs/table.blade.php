<table class="table card-table">
    <thead>
    <tr class="row--song">
        <th class="col--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="ml-1 fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        @if($col_status)
        <th class="col--status"><a href="{{ $sorts['status.title']['url'] }}"><i class="fas fa-fw fa-circle mr-2 text-secondary"></i><span class="status__title">Status</span><i class="ml-1 fa fas sort-{{ $sorts['status.title']['dir'] }} {{ ($sorts['status.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        @endif
        <th class="col--category">Category</th>
        <th class="col--pitch">Pitch</th>
        <th class="col--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="ml-1 fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--delete"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($songs as $song)
        @include('songs.index_row', ['song' => $song, 'col_status' => $col_status])
    @empty
        @include('partials.noresults-table')
    @endforelse
    </tbody>
</table>

<div class="card-footer">
    {{ $songs->count() }} songs
</div>