<div class="r-table__row row--folder sub-row--document">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <div class="item-title">
            <i class="far fa-fw fa-level-up-alt fa-rotate-90"></i>
            <i class="fad fa-fw {{ $document->getFileIcon() }} fa-swap-opacity"></i> {{ ( isset($document->title) ) ? $document->title : 'Title Unknown' }}
        </div>
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $document->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $document->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell folder-col--actions">
        <a href="{{ $document->download_url }}" class="btn btn-primary btn-sm" download="{{ $document->title }}"><i class="fa fa-fw fa-download"></i> Download</a>
    </div>
    <div class="r-table__cell col--delete">
        <a href="{{route( 'folders.documents.delete', ['folder' => $document->folder, 'document' => $document] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-times"></i></a>
    </div>
</div>
