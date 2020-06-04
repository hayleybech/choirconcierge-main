@extends('layouts.page')

@section('title', 'Riser Stacks')
@section('page-title')
<i class="fal fa-fw fa-people-arrows"></i> Riser Stacks
@endsection

@section('page-action')
    @if(Auth::user()->hasRole('Music Team'))
    <a href="{{route( 'stacks.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-plus"></i> Add New</a>
    @endif
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Riser Stacks List</h3>

        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--event">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Title</div>
                    <div class="r-table__heading col--created">Created</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('stacks.index_row', $stacks, 'stack', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $stacks->count() }} riser stacks
        </div>

    </div>

@endsection