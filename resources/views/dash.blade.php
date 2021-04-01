@extends('layouts.page')

@section('title', 'Dashboard')

@section('page-title')
<i class="fal fa-chart-line fa-fw"></i> Dashboard
@endsection

@section('page-content')

    <div class="row">
        <div class="col-md-4">

        </div>

        <div class="col-md-4">
            <x-widgets.songs :songs="$songs"></x-widgets.songs>
        </div>

        <div class="col-md-4">
            <x-widgets.birthdays :birthdays="$birthdays" :empty-dobs="$empty_dobs"></x-widgets.birthdays>
        </div>
    </div>

@endsection