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
                    <span class="mr-2 font-weight-bold text-{{ $song->my_learning->status_colour }}">
                        {{ $song->my_learning->status_name }}
                    </span>

                    @if($song->my_learning->status === 'not-started')
                        <form action="{{ route('songs.my-learning.update', $song) }}" method="post" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-link text-warning"><i class="far fa-fw fa-check"></i> I'm Assessment Ready</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-tabs nav nav-tabs">
                    <a href="#pane-status" class="card-tab nav-link active" id="tab-status" data-toggle="tab">By Status</a>
                    <a href="#pane-part" class="card-tab nav-link" id="tab-part" data-toggle="tab">By Voice Part</a>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active" id="pane-status" role="tabpanel" aria-labelledby="tab-status">

                        <div class="card-body">
                            <h4 class="mb-3">Learning Summary</h4>

                            <div class="row text-center mb-4">
                                <div class="col-6 col-md-4">
                                    <strong class="text-success">Performance Ready</strong><br>
                                    {{ $singers_performance_ready_count }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-warning">Assessment Ready</strong><br>
                                    {{ $singers_assessment_ready_count }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-danger">Learning</strong><br>
                                    {{ $singers_learning_count }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="pane-part" role="tabpanel" aria-labelledby="tab-part">

                        <div class="card-body">
                            <h4 class="mb-3">Learning Summary</h4>

                            <div class="row text-center mb-4">
                                @foreach($voice_parts_performance_ready_count as $voice_part)
                                    <div class="col-6 col-md-3">
                                        <strong>{{ $voice_part->title }}</strong><br>
                                        {{ $voice_part->performance_ready_count }} / {{ $voice_part->singers_count }}<br>
                                        <small class="text-success">Performance Ready</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
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