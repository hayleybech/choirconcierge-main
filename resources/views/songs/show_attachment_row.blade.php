<div class="r-table__row row-attachment">
    <div class="r-table__cell column--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell column--title">
        {{ ( isset($attachment->title) ) ? $attachment->title : 'Title Unknown' }}
    </div>
    <div class="r-table__cell column--filename">
        {{ $attachment->filepath }}
    </div>
    <div class="r-table__cell column--category">
        <i class="@if($attachment->category->title === 'Learning Tracks') fa fa-fw fa-file-audio @elseif($attachment->category->title === 'Full Mix (Demo)') fa fa-fw fa-compact-disc @elseif($attachment->category->title === 'Sheet Music') fa fa-fw fa-file-pdf @else fa fa-fw fa-file-alt @endif"></i>
        {{ $attachment->category->title }}
    </div>
    <div class="r-table__cell column--actions">
        <a href="{{ $attachment->download_url }}" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-download"></i> Download</a>
        <a href="{{route( 'song.attachments.delete', ['song' => $attachment->song, 'attachment' => $attachment] )}}" class="link-confirm btn btn-link text-danger btn-sm ml-2"><i class="fa fa-fw fa-times"></i></a>
    </div>
</div>

