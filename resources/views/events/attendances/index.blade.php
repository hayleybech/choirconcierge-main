@extends('layouts.page-blank')

@section('title', 'Attendance for '.$event->title . ' - Events')

@section('page-content')

    <div class="row">

        <div class="col-md-5">

            {{ Form::open(['route' => ['events.attendances.updateAll', $event]]) }}

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1 class="h2 mb-0">Attendance for: {{ $event->title }}</h1>
                </div>

                <table class="table card-table">
                    <thead>
                    <tr class="row--singer">
                        <th class="col--title">Singer</th>
                        <th class="col--attendance">Attendance</th>
                    </tr>
                    </thead>
                    <tbody>
                    @each('events.attendances.index_row', $singers, 'singer', 'partials.noresults-table')
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="100">{{ $singers->count() }} singers</td>
                    </tr>
                    </tfoot>
                </table>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a href="{{ route('events.show', ['event' => $event]) }}" class="btn btn-link">Cancel</a>
                </div>

            </div>

            {{ Form::close() }}

        </div>
    </div>
@endsection
