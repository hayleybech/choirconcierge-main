<div class="r-table__row row--folder sub-row--document">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <div class="item-title">
            <i class="far fa-fw fa-level-up-alt fa-rotate-90"></i>
            <a href="{{ $document->download_url }}" download="{{ $document->title }}">
                <i class="fad fa-fw {{ $document->getFileIcon() }} fa-swap-opacity"></i> {{ ( isset($document->title) ) ? $document->title : 'Title Unknown' }}
            </a>
        </div>
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $document->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $document->created_at->format(config('app.formats.timestamp_md')) }}
            </div>
        </div>
    </div>
    <div class="r-table__cell folder-col--actions">
        <a href="{{ $document->download_url }}" class="btn btn-secondary btn-sm btn-block" download="{{ $document->title }}"><i class="fa fa-fw fa-download"></i> Download</a>
    </div>
    <div class="r-table__cell col--delete">
        @can('update', $folder)
        <x-delete-button :action="route( 'folders.documents.destroy', ['folder' => $folder, 'document' => $document] )" />
        @endcan
    </div>
</div>
