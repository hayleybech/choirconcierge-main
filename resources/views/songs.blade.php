@extends('layouts.app')

@section('title', 'Songs')

@section('content')

    <h2 class="mb-4">Songs <a href="{{route( 'song.create' )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-user-plus"></i> Add New</a></h2>

    @if (session('status'))
        <div class="alert {{ isset($Response->error) || session('fail') ? 'alert-danger' : 'alert-success' }}" role="alert">
            {{ session('status') }}

            @isset( $Response->error )
                <pre>
			{{ var_dump($Response) }}
			@ json($args)
		</pre>
            @endisset
        </div>
    @endif

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
                <a href="{{ route('songs.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
            </div>
        </div>
    </form>

    <div class="r-table r-table--card-view-mobile">
        <div class="r-table__thead">
            <div class="r-table__row">
                <div class="r-table__heading column--mark"><input type="checkbox"></div>
                <div class="r-table__heading column--title"><a href="{{ $sorts['title']['url'] }}">Title<i class="fa fas sort-{{ $sorts['title']['dir'] }} {{ ($sorts['title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                <div class="r-table__heading column--status"><a href="{{ $sorts['status.title']['url'] }}">Status<i class="fa fas sort-{{ $sorts['status.title']['dir'] }} {{ ($sorts['status.title']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
                <div class="r-table__heading column--category">Category</div>
                <div class="r-table__heading column--pitch">Pitch</div>
                <div class="r-table__heading column--created"><a href="{{ $sorts['created_at']['url'] }}">Created<i class="fa fas sort-{{ $sorts['created_at']['dir'] }} {{ ($sorts['created_at']['current'] ? 'sort-active' : 'sort-inactive' ) }}"></i></a></div>
            </div>
        </div>
        <div class="r-table__tbody">
            @each('partials.songrow', $songs, 'song', 'partials.noresults')
        </div>
    </div>

@endsection