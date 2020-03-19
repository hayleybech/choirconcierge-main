@extends('layouts.app')

@section('title', 'Groups')

@section('content')

    <h2 class="display-4 mb-4">Groups <a href="{{route( 'groups.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-plus"></i> Add New</a></h2>

    @include('partials.flash')

    <form method="get" class="form-inline mb-0">
        @foreach( $filters as $filter )
            <div class="input-group input-group-sm mb-2 mr-2">
                <div class="input-group-prepend">
                    @php
                        $label_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary bg-primary text-white' : 'bg-light';
                    @endphp
                    <label for="{{ $filter['name']}} " class="input-group-text {{$label_class}}">{{ $filter['label'] }}</label>
                </div>
                @php
                    $field_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary' : '';
                    echo Form::select($filter['name'],
                        $filter['list'],
                        $filter['current'],
                        ['class' => 'custom-select form-control-sm ' . $field_class]
                    );
                @endphp
            </div>
        @endforeach

        <div class="input-group input-group-sm mb-2 mr-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <a href="{{ route('groups.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
            </div>
        </div>
    </form>

    <div class="r-table r-table--card-view-mobile">
        <div class="r-table__thead">
            <div class="r-table__row">
                <div class="r-table__heading column--mark"><input type="checkbox"></div>
                <div class="r-table__heading column--title">Title</div>
                <div class="r-table__heading column--slug">Slug</div>
                <div class="r-table__heading column--type">Type</div>
                <div class="r-table__heading column--created">Created</div>
                <div class="r-table__heading column--actions">Actions</div>
            </div>
        </div>
        <div class="r-table__tbody">
            @each('groups.index_row', $groups, 'group', 'partials.noresults')
        </div>
    </div>

@endsection