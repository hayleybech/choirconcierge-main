@extends('layouts.app')

@section('content')

    <div class="container-fluid px-md-5 py-5">
        @include('partials.flash')

        @yield('page-content')

    </div>

@endsection