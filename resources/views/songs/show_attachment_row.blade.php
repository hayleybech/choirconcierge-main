<div class="r-table__row row--attachment">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <div class="item-title">
            {{ ( isset($attachment->title) ) ? $attachment->title : 'Title Unknown' }}
        </div>
    </div>
    <div class="r-table__cell attachment-col--filename">
        {{ $attachment->filepath }}
    </div>
    <div class="r-table__cell attachment-col--category">
        <span class="badge badge-dark">
            <i class="@if($attachment->category->title === 'Learning Tracks') fa fa-fw fa-file-audio @elseif($attachment->category->title === 'Full Mix (Demo)') fa fa-fw fa-compact-disc @elseif($attachment->category->title === 'Sheet Music') fa fa-fw fa-file-pdf @else fa fa-fw fa-file-alt @endif"></i>
            {{ $attachment->category->title }}
        </span>
    </div>
    <div class="r-table__cell attachment-col--actions">
        <a href="{{ $attachment->download_url }}" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-download"></i> Download</a>
    </div>
    <div class="r-table__cell col--delete">
        <a href="{{route( 'song.attachments.delete', ['song' => $attachment->song, 'attachment' => $attachment] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-times"></i></a>
    </div>
</div>

