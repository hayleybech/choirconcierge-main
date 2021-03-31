<div class="r-table__row row--folder parent-row--folder">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a href="#folder-{{ $folder->id }}" data-toggle="collapse" class="collapse-toggle collapsed"></a>
        <inline-edit-field action="{{ route('folders.update', ['folder' => $folder]) }}" value="{{ $folder->title }}" csrf="{{ csrf_token() }}" edit-label="Rename" small-buttons="true">
            <x-inputs.text label="Name" id="rename_title_{{ $folder->id }}" name="title" value="{{ $folder->title }}" small="true" label-class="sr-only" class="d-inline-flex align-middle mr-2" style="width: auto; "></x-inputs.text>
        </inline-edit-field>
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
                {{ $folder->created_at->format(config('app.formats.timestamp_md')) }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        @can('delete', $folder)
        <x-delete-button :action="route( 'folders.destroy', ['folder' => $folder] )" :message="$folder->documents->count() . ' documents will also be deleted.'" />
        @endcan
    </div>

</div>

<div id="folder-{{ $folder->id }}" class="folder-documents-list collapse" data-parent="#folders-accordion">
    @forelse($folder->documents as $document)
        @include('folders.folder_document_row', ['folder' => $folder, 'document' => $document])
    @empty
        @include('partials.noresults')
    @endforelse

    @can('update', $folder)

    {{ Form::open( [ 'route' => ['folders.documents.store', $folder->id], 'method' => 'post', 'files' => 'true', 'class' => 'r-table__row row--folder sub-row--document row-add needs-validation', 'novalidate' ] ) }}
        <div class="r-table__cell col--mark">

        </div>
        <div class="r-table__cell col--title">
            <x-inputs.file label="File Upload" id="document_uploads" name="document_uploads[]" required="true" multiple="true"></x-inputs.file>
        </div>

        <div class="r-table__cell folder-col--actions">
            {{ Form::label('', '&nbsp;') }}

            <load-button theme="btn-success" size="btn-sm btn-block" icon="fa fa-plus" label="Add" label-loading="Uploading..."></load-button>
        </div>
        <div class="r-table__cell col--delete">
        </div>
    {{ Form::close() }}

    @endcan
</div>

