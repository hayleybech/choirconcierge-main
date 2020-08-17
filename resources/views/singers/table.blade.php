<table class="table card-table">
    <thead>
    <tr class="row--singer">
        <th class="col--title"><a href="{{ $sorts['name']['url'] }}">Singer<i class="ml-1 fa fas sort-{{ $sorts['name']['dir'] }} {{ ($sorts['name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--part"><a href="{{ $sorts['voice_part']['url'] }}">Part<i class="ml-1 fa fas sort-{{ $sorts['voice_part']['dir'] }} {{ ($sorts['voice_part']['current'] ? 'sort-active' : 'sort-inactive' ) }} "></i></a></th>
        @if($col_category)
        <th class="col--category"><a href="{{ $sorts['category.name']['url'] }}"><span class="status__title">Category</span><i class="ml-1 fa fas sort-{{ $sorts['category.name']['dir'] }} {{ ($sorts['category.name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        @endif
        @if($col_progress)
        <th class="col--progress">Progress</th>
        @endif
        <th class="col--actions">Actions</th>
        <th class="col--delete"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($singers as $singer)
        @include('singers.index_row', ['singer' => $singer, 'col_category' => $col_category, 'col_progress' => $col_progress])
    @empty
        @include('partials.noresults-table')
    @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="100">{{ $singers->count() }} singers</td>
        </tr>
    </tfoot>
</table>