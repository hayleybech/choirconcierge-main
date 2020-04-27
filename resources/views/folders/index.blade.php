@extends('layouts.page')

@section('title', 'Documents')
@section('page-title')
<i class="fal fa-fw fa-folders"></i> Documents
@endsection
@section('page-action')
    @if( Auth::user()->isEmployee() )
    <a href="{{route( 'folders.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-folder-plus"></i> Add Folder</a>
    @endif
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Folder List</h3>
        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--song">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Title</div>
                    <div class="r-table__heading col--created">Created</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('folders.index_row', $folders, 'folder', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $folders->count() }} folders
        </div>

    </div>

@endsection