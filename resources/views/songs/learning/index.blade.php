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
                    @each('songs.learning.index_row', $singers, 'singer', 'partials.noresults-table')
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="100">{{ $singers->count() }} singers</td>
                    </tr>
                    </tfoot>
                </table>

            </div>

        </div>
    </div>
@endsection
