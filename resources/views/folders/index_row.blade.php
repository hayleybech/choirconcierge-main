<div class="r-table__row row--folder parent-row--folder">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a href="#folder-{{ $folder->id }}" data-toggle="collapse" class="collapse-toggle collapsed">{{ ( isset($folder->title) ) ? $folder->title : 'Title Unknown' }}</a>
    </div>
    <div class="r-table__cell folder-col--status">
        {{ $folder->documents->count() }} documents
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $folder->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $folder->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell folder-col--actions">
        <a href="{{ route('folders.show', ['folder' => $folder]) }}" class="btn btn-secondary btn-sm btn-block">
            <i class="fa fa-fw fa-eye"></i> View
        </a>
    </div>
    <div class="r-table__cell col--delete">
        @if( Auth::user()->isEmployee() )
        <x-delete-button :action="route( 'folders.destroy', ['folder' => $folder] )" :message="$folder->documents()->count() . ' documents will also be deleted.'" />
        @endif
    </div>

</div>

<div id="folder-{{ $folder->id }}" class="folder-documents-list collapse" data-parent="#folders-accordion">
    @each('folders.folder_document_row', $folder->documents, 'document', 'partials.noresults')

    @if( Auth::user()->isEmployee() )

    {{ Form::open( [ 'route' => ['folders.documents.store', $folder->id], 'method' => 'post', 'files' => 'true', 'class' => 'r-table__row row--folder sub-row--document row-add needs-validation', 'novalidate' ] ) }}
        <div class="r-table__cell col--mark">

        </div>
        <div class="r-table__cell col--title">
            {{ Form::label('document_upload', 'File Upload') }}

            <div class="custom-file custom-file-sm">
                <input type="file" class="custom-file-input @error('document_upload') is-invalid @enderror" id="document_upload" name="document_upload" required>
                <div class="custom-file-label form-control-sm">Choose file</div>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please upload a file.</div>
            </div>
        </div>

        <div class="r-table__cell folder-col--actions">
            {{ Form::label('', '&nbsp;') }}

            <load-button theme="btn-success" size="btn-sm btn-block" icon="fa fa-plus" label="Add" label-loading="Uploading..."></load-button>
        </div>
        <div class="r-table__cell col--delete">
        </div>
    {{ Form::close() }}

    @endif
</div>

