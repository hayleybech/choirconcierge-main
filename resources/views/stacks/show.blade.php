@extends('layouts.page')

@section('title', $stack->title . ' - Riser Stacks')
@section('page-title', $stack->title)

@section('page-action')
    @can('update', $stack)
    <a href="{{route( 'stacks.edit', ['stack' => $stack] )}}" class="btn btn-add btn-sm btn-primary"><i class="fa fa-fw fa-edit"></i> Edit</a>
    @endcan
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

    <div class="card">
        <h3 class="card-header h4">Details</h3>

        <div class="card-body">

            <riser-stack
                :initial-rows="{{$stack->rows}}"
                :initial-cols="{{ $stack->columns }}"
                :initial-front-row-length="{{ $stack->front_row_length }}"
                :initial-singers="{{ $stack->singers->toJson() }}"
                :initial-voice-parts="{{ $voice_parts->toJson() }}"
                :initial-front-row-on-floor="{{ var_export($stack->front_row_on_floor, true) }}"
                :edit-disabled="true"
                
            ></riser-stack>

        </div>
    </div>



@endsection