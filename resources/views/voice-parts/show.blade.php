@extends('layouts.page')

@section('title', $voice_part->title . ' - Voice Parts')
@section('page-title', $voice_part->title)
@section('page-action')
	<a href="{{ route( 'voice-parts.edit', ['voice_part' => $voice_part] ) }}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection
@section('page-lead')
    <span class="d-flex align-items-center">Colour: <span class="mx-2 rounded" style="width: 1em; height: 1em; border: 1px solid #ddd; background-color: {{ $voice_part->colour }};"></span> <code>{{ $voice_part->colour }}</code></span>
	Created:
	<span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($voice_part->created_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($voice_part->created_at)->format('M d, H:i') }}
        </span>
    </span><br>
	Updated: <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ optional($voice_part->updated_at)->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ optional($voice_part->updated_at)->format('M d, H:i') }}
        </span>
    </span><br>
@endsection

@section('page-content')


@endsection