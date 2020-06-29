<div class="r-table__row row--group">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('groups.show', ['group' => $group])}}">
        {{ $group->title ?? 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell group-col--type">
        <span class="badge badge-dark">{{ ucwords($group->list_type) }}</span>
    </div>
    <div class="r-table__cell group-col--slug">
        <span class="badge badge-light">{{ $group->slug }}@</span>
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $group->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $group->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        <x-delete-button :action="route( 'groups.destroy', ['group' => $group] )" :message="'Any future emails addressed to this group will be lost!'" />
    </div>

</div>

