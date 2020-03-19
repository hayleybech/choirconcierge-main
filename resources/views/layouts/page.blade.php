@extends('layouts.app')

@section('content')

    <div class="jumbotron bg-light">
        <h2 class="display-4">@yield('page-title') @yield('page-action')</h2>
        <p class="lead">
            @yield('page-lead')
        </p>
    </div>

    @include('partials.flash')

    @yield('page-content')

@endsection