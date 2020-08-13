@extends('layouts.page')

@section('title', 'Riser Stacks')
@section('page-title')
    <i class="fal fa-fw fa-people-arrows"></i> Riser Stacks
    @can('create', \App\Models\RiserStack::class)
        <a href="{{route( 'stacks.create' )}}" class="btn btn-add btn-sm btn-primary ml-2"><i class="fa fa-fw fa-plus"></i> Add New</a>
    @endcan
@endsection

@section('page-action')
@endsection

@section('page-content')

    <div class="card">
        <h3 class="card-header h4">Riser Stacks List</h3>

        <table class="table card-table">
            <thead>
                <tr class="row--event">
                    <th class="col--title">Title</th>
                    <th class="col--created">Created</th>
                    <th class="col--delete"></th>
                </tr>
            </thead>
            <tbody>
                @each('stacks.index_row', $stacks, 'stack', 'partials.noresults')
            </tbody>
        </table>

        <div class="card-footer">
            {{ $stacks->count() }} riser stacks
        </div>

    </div>

@endsection