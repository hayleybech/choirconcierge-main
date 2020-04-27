@extends('layouts.page')

@section('title', $folder->title . ' - Folders')
@section('page-title', $folder->title)
@section('page-action')
    @if( Auth::user()->isEmployee() )
    <a href="{{ route( 'folders.edit', ['folder' => $folder] ) }}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endif
@endsection

