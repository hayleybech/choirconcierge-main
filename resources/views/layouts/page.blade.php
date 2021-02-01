@extends('layouts.tenant')

@section('tenant-content')

    <div class="page-header mt-4">
        <div class="container-fluid px-md-5">

            <h1 class="d-flex justify-content-between align-items-center"><span>@yield('page-title')</span> <span>@yield('page-action')</span></h1>

            <p class="lead">
                @yield('page-lead')
            </p>
        </div>
    </div>

    <div class="container-fluid px-md-5">
        @include('partials.flash')

        @yield('page-content')

    </div>

@endsection