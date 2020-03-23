@extends('layouts.page')

@section('title', 'Groups')
@section('page-title', 'Groups')

@section('page-action')
<a href="{{route( 'groups.create' )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-plus"></i> Add New</a>
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Groups List</h3>

        <div class="card-body">

            <?php
                /*
            use App\Models\UserGroup;
            $filters_class = UserGroup::hasActiveFilters() ? 'btn-primary' : 'btn-outline-secondary';
            ?>
            <a class="btn btn-sm {{ $filters_class }}" data-toggle="collapse" href="#filters" role="button" aria-expanded="false" aria-controls="filters"><i class="fa fa-filter"></i> Filter</a>

            <div class="collapse mt-2" id="filters">

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

            </div>*/?>

        </div>


        <div class="r-table r-table--card-view-mobile">
            <div class="r-table__thead">
                <div class="r-table__row row--group">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Title</div>
                    <div class="r-table__heading group-col--slug">Slug</div>
                    <div class="r-table__heading group-col--type">Type</div>
                    <div class="r-table__heading col--created">Created</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>
            <div class="r-table__tbody">
                @each('groups.index_row', $groups, 'group', 'partials.noresults')
            </div>
        </div>

        <div class="card-footer">
            {{ $groups->count() }} groups
        </div>

    </div>

@endsection