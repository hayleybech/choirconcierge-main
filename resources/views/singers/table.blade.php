<table class="table card-table">
    <thead>
    <tr class="row--singer">
        <th class="col--title"><a href="{{ $sorts['name']['url'] }}">Singer<i class="fa fas sort-{{ $sorts['name']['dir'] }} {{ ($sorts['name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--part"><a href="{{ $sorts['voice_part']['url'] }}">Part<i class="fa fas sort-{{ $sorts['voice_part']['dir'] }} {{ ($sorts['voice_part']['current'] ? 'sort-active' : 'sort-inactive' ) }} "></i></a></th>
        <th class="col--category"><a href="{{ $sorts['category.name']['url'] }}"><span class="status__title">Category</span><i class="fa fas sort-{{ $sorts['category.name']['dir'] }} {{ ($sorts['category.name']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></th>
        <th class="col--progress">Progress</th>
        <th class="col--actions">Actions</th>
        <th class="col--delete"></th>
    </tr>
    </thead>
    <tbody>
    @each('singers.index_row', $singers, 'singer', 'partials.noresults-table')
    </tbody>
</table>

<div class="card-footer">
    {{ $singers->count() }} singers
</div>