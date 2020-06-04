@extends('layouts.page')

@section('title', $stack->title . ' - Riser Stacks')
@section('page-title', $stack->title)

@section('page-action')
    @if(Auth::user()->hasRole('Music Team'))
    <a href="{{route( 'stacks.edit', ['stack' => $stack] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endif
@endsection

@section('page-lead')
    Rows: {{$stack->rows}}<br>
    Columns: {{$stack->columns}}<br>
    Created:
    <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ $stack->created_at->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ $stack->created_at->format('M d, H:i') }}
        </span>
    </span><br>
    Updated: <span class="date" style="display: inline-flex;">
        <span class="date__diff-for-humans">
            {{ $stack->updated_at->diffForHumans() }}
        </span>
        <span class="date__regular">
            {{ $stack->updated_at->format('M d, H:i') }}
        </span>
    </span><br>
@endsection

@section('page-content')

    <div class="card bg-light">
        <h3 class="card-header h4">Details</h3>

        <div class="card-body">

            <riser-stack :rows="{{$stack->rows}}" :cols="{{ $stack->columns }}" :initial-singers="{{ $stack->singers->toJson() }}" :edit-disabled="true"></riser-stack>

        </div>
    </div>



@endsection