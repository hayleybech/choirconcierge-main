@extends('layouts.app')

@section('title', 'Main menu')

@section('content')
    
    <h2 class="display-4 mb-4">{{$song->title}} <a href="{{route( 'song.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="mb-2 text-muted">
        Status: {{ $song->status->title }}
    </p>
    <p class="mb-2 text-muted">
        Category:
        <ul class="list-inline">
        @foreach( $song->categories as $cat )
        <li class="list-inline-item">{{ $cat->title }}</li>
        @endforeach
        </ul>
    </p>
    <p class="mb-2 text-muted">
        Pitch Blown: {{ $song->getPitchBlown() }}
    </p>

    <h3>Attachments</h3>

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
            @each('partials.attachment', $song->attachments, 'attachment', 'partials.noresults')
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
                            <i class="fa fa-fw fa-plus"></i> Add</a>
                        </button>
                    </div>
            {{ Form::close() }}

        </div>
    </div>

@endsection