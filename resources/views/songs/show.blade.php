@extends('layouts.page')

@section('title', $song->title . ' - Songs')
@section('page-title', $song->title)
@section('page-action')
    <a href="{{route( 'song.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection
@section('page-lead')
    <span class="badge badge-light">{{ $song->status->title }}</span><br>
    Category:
    @foreach( $song->categories as $cat )
        <span class="song-category">{{ $cat->title }}</span>@if( ! $loop->last ), @endif
    @endforeach
    <br>
    <button class="pitch btn btn-secondary btn-sm"><i class="fa fa-play"></i> <span class="key">{{ $song->pitch }}</span></button><br>
@endsection

@section('page-content')

    <div class="card bg-light">
        <h4 class="card-header">Attachments</h4>
        <div class="card-body">

            <div class="r-table r-table--card-view-mobile">
                <div class="r-table__thead">
                    <div class="r-table__row row-attachment">
                        <div class="r-table__heading column--mark"><input type="checkbox"></div>
                        <div class="r-table__heading column--title">Title</div>
                        <div class="r-table__heading column--filename">File</div>
                        <div class="r-table__heading column--category">Category</div>
                        <div class="r-table__heading column--actions">Actions</div>
                    </div>
                </div>
                <div class="r-table__tbody">
                    @each('songs.show_attachment_row', $song->attachments, 'attachment', 'partials.noresults')
                </div>
                <div class="r-table__tfoot">

                    {{ Form::open( [ 'route' => ['song.attachments.store', $song->id], 'method' => 'post', 'files' => 'true', 'class' => 'r-table__row row-attachment row-add needs-validation', 'novalidate' ] ) }}
                    <div class="r-table__cell column--mark">

                    </div>
                    <div class="r-table__cell column--title">
                        {{ Form::label('title', 'Title') }}
                        <input id="title" name="title" type="text" required class="form-control form-control-sm @error('title') is-invalid @enderror">
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please type a file name.</div>
                    </div>
                    <div class="r-table__cell column--filename">
                        {{ Form::label('attachment_upload', 'File Upload') }}

                        <div class="custom-file custom-file-sm">
                            <input type="file" class="custom-file-input @error('attachment_upload') is-invalid @enderror" id="attachment_upload" name="attachment_upload" required>
                            <div class="custom-file-label form-control-sm">Choose file</div>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please upload a file.</div>
                        </div>

                    </div>
                    <div class="r-table__cell column--category">
                        {{ Form::label('category', 'Category') }}
                        {{ Form::select('category',
                            $categories_keyed,
                            '',
                            ['required', 'class' => 'custom-select custom-select-sm']
                        ) }}
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please choose a category.</div>
                    </div>
                    <div class="r-table__cell column--actions">
                        {{ Form::label('', '&nbsp;') }}

                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-fw fa-plus"></i> Add
                        </button>
                    </div>
                    {{ Form::close() }}

                </div>
            </div>

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