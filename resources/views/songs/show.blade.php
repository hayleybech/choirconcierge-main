@extends('layouts.page')

@section('title', $song->title . ' - Songs')

@section('page-content')
    <div class="row">
        <div class="col-md-5">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1>{{ $song->title }}</h1>

                    @if(Auth::user()->hasRole('Music Team'))
                        <a href="{{route( 'songs.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-secondary ml-2 flex-shrink-0"><i class="fa fa-fw fa-edit"></i> Edit</a>
                    @endif
                </div>

                <div class="card-body">
                    {{ $song->status->title }}<br>
                    Category:
                    @foreach( $song->categories as $cat )
                        <span class="song-category badge badge-light">{{ $cat->title }}</span>
                    @endforeach
                    <br>
                    <button class="pitch btn btn-secondary btn-sm"><i class="fa fa-play"></i> <span class="key">{{ $song->pitch }}</span></button><br>
                </div>
            </div>


            <div class="card">
                <h4 class="card-header">Attachments</h4>

                <track-list-player :song="{{ $song->toJson() }}" :attachments="{{ $song->attachments->toJson() }}"></track-list-player>

                @if(Auth::user()->hasRole('Music Team'))
                <div class="card-body">
                    {{ Form::open( [ 'route' => ['songs.attachments.store', $song->id], 'method' => 'post', 'files' => 'true', 'class' => 'needs-validation', 'novalidate' ] ) }}

                    <div class="d-flex flex-column flex-md-row align-items-sm-stretch align-items-md-end">
                        <div class="mr-2 mb-2">
                            {{ Form::label('attachment_upload', 'File Upload') }}

                            <div class="custom-file custom-file-sm">
                                <input type="file" class="custom-file-input @error('attachment_upload') is-invalid @enderror" id="attachment_upload" name="attachment_upload" required>
                                <div class="custom-file-label form-control-sm">Choose file</div>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please upload a file.</div>
                            </div>

                        </div>
                        <div class="mr-2 mb-2">
                            {{ Form::label('category', 'Category') }}
                            {{ Form::select('category',
                                $categories_keyed,
                                '',
                                ['required', 'class' => 'custom-select custom-select-sm']
                            ) }}
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please choose a category.</div>
                        </div>
                        <div class="flex-shrink-0 mb-2">
                            <load-button theme="btn-success" size="btn-sm" icon="fa fa-plus" label="Add" label-loading="Uploading..."></load-button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                @endif

                <div class="card-footer">
                    {{ $song->attachments->count() }} attachments
                </div>
            </div>
            
        </div>

        <div class="col-md-7">
        </div>
    </div>


    @push('scripts-footer-bottom')
        <script src="{{ asset('js/lib/Tone.js') }}"></script>
        <script src="{{ asset('js/Pitch_Pipe.js') }}"></script>
        <script>
            let synth = new Tone.Synth().toMaster();
            let pipe = new Pitch_Pipe( '.pitch', @json($song->pitch) );
        </script>
    @endpush

@endsection