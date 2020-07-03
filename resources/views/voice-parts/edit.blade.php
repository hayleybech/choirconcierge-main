@extends('layouts.page')

@section('title', 'Edit - ' . $voice_part->title)
@section('page-title', $voice_part->title)

@section('page-content')

{{ Form::open( [ 'route' => ['voice-parts.update', $voice_part], 'method' => 'put' ] ) }}

<div class="row">
	<div class="col-md-6">

		<div class="card">
			<div class="card-header"><h3 class="h4">Edit Voice Part</h3></div>

			<div class="card-body">
				<p>
					{{ Form::label('title', 'Name') }}
					{{ Form::text('title', $voice_part->title, ['class' => 'form-control']) }}
				</p>

				<p>
					{{ Form::label('colour', 'Colour') }}
					{{ Form::color('colour', $voice_part->colour, ['class' => 'form-control']) }}
				</p>

			</div>

			<div class="card-footer">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-fw fa-check"></i> Save
				</button>
				<a href="{{ route('voice-parts.show', $voice_part) }}" class="btn btn-outline-secondary">
					<i class="fa fa-fw fa-times"></i> Cancel
				</a>
			</div>
		</div>

	</div>
</div>

{{ Form::close() }}

@endsection