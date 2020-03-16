@extends('layouts.app')

@section('title', $group->title . ' - Groups')

@section('content')
    
    <!--suppress VueDuplicateTag -->
    <h2 class="display-4 mb-4">{{$group->title}} <a href="{{route( 'groups.edit', ['group' => $group] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

    @include('partials.flash')

    <p class="mb-2 text-muted">
        Group Slug: {{ $group->slug }}
    </p>

    <p class="mb-2 text-muted">
        List Type: {{ $group->list_type }}
    </p>

@endsection