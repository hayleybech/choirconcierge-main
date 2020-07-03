@extends('layouts.page')

@section('title', $song->title . ' - Songs')
@section('page-title', $song->title)
@section('page-action')
    @if(Auth::user()->hasRole('Music Team'))
    <a href="{{route( 'songs.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endif
@endsection
@section('page-lead')
    {{ $song->status->title }}<br>
    Category:
    @foreach( $song->categories as $cat )
        <span class="song-category badge badge-light">{{ $cat->title }}</span>
    @endforeach
    <br>
    <button class="pitch btn btn-secondary btn-sm"><i class="fa fa-play"></i> <span class="key">{{ $song->pitch }}</span></button><br>
@endsection

@section('page-content')

    <div class="card">
        <h4 class="card-header">Attachments</h4>
        <div class="r-table r-table--card-view-mobile">

            <div class="r-table__thead">
                <div class="r-table__row row--attachment">
                    <div class="r-table__heading col--mark"><input type="checkbox"></div>
                    <div class="r-table__heading col--title">Title</div>
                    <div class="r-table__heading attachment-col--filename">File</div>
                    <div class="r-table__heading attachment-col--category">Category</div>
                    <div class="r-table__heading attachment-col--actions">Actions</div>
                    <div class="r-table__heading col--delete"></div>
                </div>
            </div>

            <track-list :song="{{ $song->toJson() }}" :attachments="{{ $song->attachments->toJson() }}"></track-list>
            
            @if(Auth::user()->hasRole('Music Team'))
            <div class="r-table__tfoot">

                {{ Form::open( [ 'route' => ['songs.attachments.store', $song->id], 'method' => 'post', 'files' => 'true', 'class' => 'r-table__row row--attachment row-add needs-validation', 'novalidate' ] ) }}
                <div class="r-table__cell col--mark">

                </div>
                <div class="r-table__cell col--title">
                    {{ Form::label('title', 'Title') }}
                    <input id="title" name="title" type="text" required class="form-control form-control-sm @error('title') is-invalid @enderror">
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please type a file name.</div>
                </div>
                <div class="r-table__cell attachment-col--filename">
                    {{ Form::label('attachment_upload', 'File Upload') }}

                    <div class="custom-file custom-file-sm">
                        <input type="file" class="custom-file-input @error('attachment_upload') is-invalid @enderror" id="attachment_upload" name="attachment_upload" required>
                        <div class="custom-file-label form-control-sm">Choose file</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please upload a file.</div>
                    </div>

                </div>
                <div class="r-table__cell attachment-col--category">
                    {{ Form::label('category', 'Category') }}
                    {{ Form::select('category',
                        $categories_keyed,
                        '',
                        ['required', 'class' => 'custom-select custom-select-sm']
                    ) }}
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please choose a category.</div>
                </div>
                <div class="r-table__cell attachment-col--actions">
                    {{ Form::label('', '&nbsp;') }}

                    <load-button theme="btn-success" size="btn-sm" icon="fa fa-plus" label="Add" label-loading="Uploading..."></load-button>
                </div>
                <div class="r-table__cell col--delete">
                </div>
                {{ Form::close() }}

            </div>
            @endif
        </div>

        <div class="card-footer">
            {{ $song->attachments->count() }} attachments
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