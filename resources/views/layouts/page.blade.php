@extends('layouts.app')

@section('content')

    <div class="page-header mt-4">
        <div class="container-fluid px-5">

            {{ Breadcrumbs::render() }}

            <h2 class="display-4 d-flex justify-content-between align-items-center"><span>@yield('page-title')</span> <span>@yield('page-action')</span></h2>

            <p class="lead">
                @yield('page-lead')
            </p>
        </div>
    </div>

    <div class="container-fluid px-5">
        @include('partials.flash')

        @yield('page-content')

    </div>

@endsection