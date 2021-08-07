@extends('layouts.page')

@section('title', $song->title . ' - Songs')

@section('page-content')
    <div class="row">
        <div class="col-md-5">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1 class="h2 mb-0">{{ $song->title }}</h1>

                    @can('update', $song)
                    <a href="{{route( 'songs.edit', ['song' => $song] )}}" class="btn btn-add btn-sm btn-primary ml-2 flex-shrink-0"><i class="fa fa-fw fa-edit"></i> Edit</a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div class="mr-2 mb-4">
                            <pitch-button note="{{ explode('/',$song->pitch)[0] }}"></pitch-button>
                        </div>

                        <div class="mr-2 mb-4 text-{{ $song->status->colour }} font-weight-bold">
                            @if('Pending' === $song->status->title)
                                <i class="fas fa-fw fa-lock mr-2"></i>
                            @else
                                <i class="fas fa-fw fa-circle mr-2"></i>
                            @endif
                            {{ $song->status->title }}
                        </div>

                        <div class="mb-4">
                            @foreach( $song->categories as $cat )
                                <span class="song-category badge badge-pill badge-secondary">{{ $cat->title }}</span>
                            @endforeach
                        </div>
                    </div>

                    <h4>My Learning Status</h4>
                    <span class="mr-2">
                        {{ $song->my_learning->status_name }}
                    </span>

                    @if($song->my_learning->status === 'not-started')
                        <form action="{{ route('songs.my-learning.update', $song) }}" method="post" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-link text-success btn-sm"><i class="far fa-fw fa-check"></i> I'm Assessment Ready</button>
                        </form>
                    @endif
                </div>
            </div>


            <div class="card">
                <h4 class="card-header">Attachments</h4>

                <track-list-player :song="{{ $song->toJson() }}" :attachments="{{ $song->attachments->toJson() }}" @can('update', $song):can-update="true"@endcan></track-list-player>

                @can('update', $song)
                <div class="card-body">
                    {{ Form::open( [ 'route' => ['songs.attachments.store', $song->id], 'method' => 'post', 'files' => 'true', 'class' => 'needs-validation', 'novalidate' ] ) }}

                    <div class="d-flex flex-column flex-md-row align-items-sm-stretch align-items-md-end">
                        <div class="mr-2 mb-2">
                            <x-inputs.file label="File Upload" id="attachment_uploads" name="attachment_uploads[]" required="true" multiple="true"></x-inputs.file>
                        </div>
                        <div class="mr-2 mb-2">
                            <x-inputs.select label="Category" id="category" name="category" :options="$categories_keyed" small="true"></x-inputs.select>
                        </div>
                        <div class="flex-shrink-0 mb-2">
                            <load-button theme="btn-success" size="btn-sm" icon="fa fa-plus" label="Add" label-loading="Uploading..."></load-button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                @endcan

                <div class="card-footer">
                    {{ $song->attachments->count() }} attachments
                </div>
            </div>
            
        </div>

        <div class="col-md-7">
        </div>
    </div>

@endsection