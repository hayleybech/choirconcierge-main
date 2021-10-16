@extends('layouts.page-blank')
@section('title', 'Attendance Report - Events')

@section('page-content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start">
            <h1 class="h2 mb-0">Attendance Report</h1>
            <div class="mt-2" id="filters">

                <form method="get" class="form-inline mb-0">
                    @each('partials.filter', $filters, 'filter')

                    <div class="input-group input-group-sm mb-2 mr-2">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> Apply</button>
                            <a href="{{ route('events.reports.attendance') }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table card-table table-report-attendance table-responsive">
            <thead>
            <tr class="row--singer">
                <th class="th-diagonal">
                    <div><span>&nbsp;</span></div>
                </th>
                @foreach($events as $event)
                <th class="th-diagonal">
                    <div><span>{{ $event->title }}</span></div>
                </th>
                @endforeach
                <th class="th-diagonal">
                    <div><span>Events present</span></div>
                </th>
            </tr>
            <tr>
                <th class="th-date"></th>
                @foreach($events as $event)
                <th class="th-date">
                    {{ $event->start_date->format('Y') }}<br>
                    {{ $event->start_date->format('m-d') }}
                </th>
                @endforeach
                <th class="th-date"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($voice_parts as $voice_part)
                <tr>
                    <th class="th-part" colspan="100000">{{ $voice_part->title }}</th>
                </tr>
                @foreach($voice_part->singers as $singer)
                    @include('events.reports.attendance_row', ['singers' => $voice_part->singers, 'events' => $events])
                @endforeach
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Singers present</td>
                @foreach($events as $event)
                <td>
                    <small>
                        {{ floor($event->singers_attendance('present')->active()->get()->count() / \App\Models\Singer::active()->count() * 100) }}%<br>
                        ({{ $event->singers_attendance('present')->active()->get()->count() }}/{{ \App\Models\Singer::active()->count() }})
                    </small>
                </td>
                @endforeach
                <td></td>
            </tr>
            </tfoot>
        </table>

        <div class="card-footer">
            Average singers per event: {{ $avg_singers_per_event }} | Average events per singer {{ $avg_events_per_singer }}
        </div>

    </div>
@endsection