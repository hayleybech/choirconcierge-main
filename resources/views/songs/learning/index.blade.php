@extends('layouts.page-blank')

@section('title', 'Learning Status for '.$song->title . ' - Songs')

@section('page-content')

    <div class="row">

        <div class="col-md-8">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1 class="h2 mb-0">Learning Status for: {{ $song->title }}</h1>
                </div>

                <table class="table card-table table-attendance">
                    <thead>
                        <tr class="row--singer">
                            <th class="col--title">Singer</th>
                            <th class="col--attendance">Status</th>
                            <th class="col--attendance">Mark As</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($voice_parts as $voice_part)
                        <tr>
                            <th colspan="100000" class="d-block d-sm-table-cell" style="background-color: #ddd; border-bottom: 1px solid #ccc;">{{ $voice_part->title }}</th>
                        </tr>
                        @each('songs.learning.index_row', $voice_part->singers, 'singer', 'partials.noresults-table')
                    @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>
@endsection
