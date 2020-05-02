@extends('layouts.page')

@section('title', $folder->title . ' - Folders')
@section('page-title', $folder->title)
@section('page-action')
    @if( Auth::user()->isEmployee() )
    <a href="{{ route( 'folders.edit', ['folder' => $folder] ) }}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endif
@endsection

@section('page-content')

    <div class="card bg-light">
        <h4 class="card-header">Documents</h4>
        <div class="r-table r-table--card-view-mobile">

            <div class="r-table__thead">
                <div class="r-table__row row--attachment">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Filename</div>
                    <div class="r-table__heading attachment-col--actions">Actions</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('folders.show_document_row', $folder->documents, 'document', 'partials.noresults')
            </div>
            @if( Auth::user()->isEmployee() )
                <div class="r-table__tfoot">

                    {{ Form::open( [ 'route' => ['folders.documents.store', $folder->id], 'method' => 'post', 'files' => 'true', 'class' => 'r-table__row row--attachment row-add needs-validation', 'novalidate' ] ) }}
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

                    <div class="r-table__cell attachment-col--actions">
                        {{ Form::label('', '&nbsp;') }}

                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-fw fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="r-table__cell col--delete">
                    </div>
                    {{ Form::close() }}

                </div>
            @endif
        </div>

        <div class="card-footer">
            {{ $folder->documents->count() }} documents
        </div>
    </div>

@endsection