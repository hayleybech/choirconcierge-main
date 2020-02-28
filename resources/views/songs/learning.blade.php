@extends('layouts.app')

@section('title', 'Learning Mode - Songs')

@section('content')

    <h2 class="display-4 mb-4">Learning Mode</h2>

    @include('partials.flash')

    <form method="get" class="form-inline mb-0">
        @foreach( $filters as $filter )
            <div class="input-group input-group-sm mb-2 mr-2">
                <div class="input-group-prepend">
                    @php
                        $label_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary bg-primary text-white' : 'bg-light';
                    @endphp
                    <label for="{{ $filter['name']}} " class="input-group-text {{$label_class}}">{{ $filter['label'] }}</label>
                </div>
                @php
                    $field_class = ( $filter['current'] !== $filter['default'] ) ? 'border-primary' : '';
                    echo Form::select($filter['name'],
                        $filter['list'],
                        $filter['current'],
                        ['class' => 'custom-select form-control-sm ' . $field_class]
                    );
                @endphp
            </div>
        @endforeach

        <div class="input-group input-group-sm mb-2 mr-2">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                <a href="{{ route('songs.index') }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Clear</a>
            </div>
        </div>
    </form>


    <div class="learning-window">

        <div class="audio-panel">
            <div class="audio-controls">
                <div class="track-title">Full Mix - Touch of Paradise</div>
                <input type="range" class="audio-seek" value="0" min="0" max="1" step="0.001">
                <div class="play-controls">
                    <!--<button class="prev btn btn-secondary"><i class="fa fa-fast-backward"></i></button>-->
                    <button class="play btn btn-primary"><i class="fa fa-play"></i></button>
                    <button class="pause btn btn-primary"><i class="fa fa-pause"></i></button>
                    <!--<button class="next btn btn-secondary"><i class="fa fa-fast-forward"></i></button>-->

                    <button class="pitch btn btn-secondary"><i class="fa fa-play"></i> <span class="key">C</span></button>

                    <div class="time">
                        <span class="time-position"></span> / <span class="time-length"></span>
                    </div>
                </div>
            </div>
            <div class="song-list list-group">
            @foreach($songs as $song)
                <a href="#" data-id="{{$loop->index}}" class="open-song list-group-item list-group-item-action @if($loop->first) active @endif"><i class="fa @if($loop->first) fa-folder-open @else fa-folder @endif mr-2"></i> {{$song->title}}</a>
            @endforeach
            </div>
            <div class="track-list list-group">

            </div>
        </div>

        <div class="viewer-panel">
            <canvas id="the-canvas"></canvas>

            <div class="page-controls">
                <button id="prev" class="btn btn-secondary"><i class="fa fa-fw fa-arrow-left"></i></button>
                <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>
                <button id="next" class="btn btn-secondary"><i class="fa fa-fw fa-arrow-right"></i></button>
            </div>
        </div>

    </div>

@endsection

@push('scripts-footer-bottom')
    <script src="{{ asset('js/lib/howler.js') }}"></script>
    <script src="{{ asset('js/lib/pdfjs-2-3-200/pdf.js') }}"></script>
    <script src="{{ asset('js/lib/pdfjs-2-3-200/pdf.worker.js') }}"></script>
    <script src="{{ asset('js/lib/Tone.js') }}"></script>

    <script src="{{ asset('js/viewer.js') }}"></script>
    <script src="{{ asset('js/player.js') }}"></script>
    <script src="{{ asset('js/learning_viewer.js') }}"></script>

    <script>
        let songs = @json($songs);
        let lv = new LearningViewer(document.querySelector('.learning-window'), songs);
    </script>

@endpush