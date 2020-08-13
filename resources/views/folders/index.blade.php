@extends('layouts.page')

@section('title', 'Documents')
@section('page-title')
    <i class="fal fa-fw fa-folders"></i> Documents
    @can('create', \App\Models\Folder::class)
        <a href="{{route( 'folders.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-folder-plus"></i> Add Folder</a>
    @endcan
@endsection
@section('page-action')
@endsection

@section('page-content')

    <div class="card">
        <h3 class="card-header h4"></h3>
        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--folder">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Title</div>
                    <div class="r-table__heading folder-col--status">Documents</div>
                    <div class="r-table__heading col--created">Created</div>
                    <div class="r-table__heading folder-col--actions">Actions</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div id="folders-accordion" class="r-table__tbody accordion">
                @each('folders.index_row', $folders, 'folder', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $folders->count() }} folders
        </div>

    </div>

@endsection