@extends('layouts.app')

@section('content')

    <div class="page-header jumbotron jumbotron-fluid bg-light bg-gradient-blue-purple text-white">
        <div class="container">

            <h2 class="display-4">@yield('page-title') @yield('page-action')</h2>

            {{ Breadcrumbs::render() }}

            <p class="lead">
                @yield('page-lead')
            </p>
        </div>
    </div>

    <div class="container">
        @include('partials.flash')

        @yield('page-content')

    </div>

@endsection