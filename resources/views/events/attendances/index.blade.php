@extends('layouts.page-blank')

@section('title', 'Attendance for '.$event->title . ' - Events')

@section('page-content')

    <div class="row">

        <div class="col-md-8">

            {{ Form::open(['route' => ['events.attendances.updateAll', $event]]) }}

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1 class="h2 mb-0">Attendance for: {{ $event->title }}</h1>
                </div>

                <table class="table card-table table-attendance">
                    <thead>
                        <tr class="row--singer">
                            <th class="col--title">Singer</th>
                            <th class="col--attendance">Did they attend?</th>
                            <th class="col--attendance">Reason for absence</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($voice_parts as $voice_part)
                        <tr>
                            <th colspan="100000" class="d-block d-sm-table-cell" style="background-color: #ddd; border-bottom: 1px solid #ccc;">{{ $voice_part->title }}</th>
                        </tr>
                        @each('events.attendances.index_row', $voice_part->singers, 'singer', 'partials.noresults-table')
                    @endforeach
                    </tbody>
                </table>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-check"></i> Save</button>
                    <a href="{{ route('events.show', ['event' => $event]) }}" class="btn btn-link text-danger"><i class="fa fa-fw fa-times"></i>  Cancel</a>
                </div>

            </div>

            {{ Form::close() }}

        </div>
    </div>
@endsection
