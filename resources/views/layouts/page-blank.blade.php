@extends('layouts.app')

@section('content')

    <div class="container-fluid px-5 py-5">
        @include('partials.flash')

        @yield('page-content')

    </div>

@endsection