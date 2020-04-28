<div class="r-table__row row--attachment">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <div class="item-title">
            {{ ( isset($document->title) ) ? $document->title : 'Title Unknown' }}
        </div>
    </div>
    <div class="r-table__cell attachment-col--filename">
        {{ $document->filepath }}
    </div>
    <div class="r-table__cell attachment-col--actions">
        <a href="{{ $document->download_url }}" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-download"></i> Download</a>
    </div>
    <div class="r-table__cell col--delete">
        <a href="{{route( 'folders.documents.delete', ['folder' => $document->folder, 'document' => $document] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-times"></i></a>
    </div>
</div>

