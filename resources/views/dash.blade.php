@extends('layouts.page')

@section('title', 'Dashboard')

@section('page-title')
<i class="fal fa-chart-line fa-fw"></i> Dashboard
@endsection

@section('page-content')

    <div class="row">
        <div class="col-md-4">
            @can('viewAny', \App\Models\Event::class)
            <x-widgets.events :events="$events"></x-widgets.events>
            @endcan
        </div>

        <div class="col-md-4">
            @can('viewAny', \App\Models\Song::class)
            <x-widgets.songs :songs="$songs"></x-widgets.songs>
            @endcan
        </div>

        <div class="col-md-4">
            @can('viewAny', \App\Models\Singer::class)
            <x-widgets.birthdays :birthdays="$birthdays" :empty-dobs="$empty_dobs"></x-widgets.birthdays>
            <x-widgets.memberversaries :memberversaries="$memberversaries"></x-widgets.memberversaries>
            @endcan
        </div>
    </div>

@endsection