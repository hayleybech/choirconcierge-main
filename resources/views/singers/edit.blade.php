@extends('layouts.page')

@section('title', 'Edit - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

    {{ Form::open( [ 'route' => ['singers.show', $singer->id], 'method' => 'put', 'files' => true ] ) }}

    <div class="row">
        <div class="col-md-6">

            <div class="card">
                <div class="card-header"><h3 class="h4">Edit Singer</h3></div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('first_name', 'First Name') }}
                                {{ Form::text('first_name', $singer->first_name, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('last_name', 'Last Name') }}
                                {{ Form::text('last_name', $singer->last_name, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="avatar">Profile Picture</label>
                        <div class="custom-file custom-file-sm">
                            <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                            <div class="custom-file-label form-control-sm">Choose file</div>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please upload a valid image file.</div>
                        </div>
                    </div>

                    <p>
                        {{ Form::label('email', 'E-Mail Address') }}
                        {{ Form::email('email', $singer->email, ['class' => 'form-control']) }}
                    </p>

                    <p>
                        {{ Form::label('password', 'Change Password') }}
                        {{ Form::password('password', ['class' => 'form-control']) }}
                    </p>
                    <p>
                        {{ Form::label('password_confirmation', 'Confirm Password') }}
                        {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                    </p>

                    @if(Auth::user()->can('create', \App\Models\Profile::class))
                        <fieldset class="form-group">
                            <legend class="col-form-label">Onboarding</legend>

                            <x-inputs.radio id="onboarding_enabled_yes" name="onboarding_enabled" value="1" :checked="$singer->onboarding_enabled">
                                <x-slot name="label">
                                    Enabled
                                    <small class="text-muted ml-2">
                                        Choose this option for new/prospective singers.
                                    </small>
                                </x-slot>
                            </x-inputs.radio>

                            <x-inputs.radio id="onboarding_enabled_no" name="onboarding_enabled" value="0" :checked="! $singer->onboarding_enabled">
                                <x-slot name="label">
                                    Disabled
                                    <small class="text-muted ml-2">
                                        Choose this option when you're adding existing singers.
                                    </small>
                                </x-slot>
                            </x-inputs.radio>
                        </fieldset>

                        <div class="form-group">
                            {{ Form::label('voice_part_id', 'Voice Part') }}
                            {{ Form::select('voice_part_id', $voice_parts, optional($singer->voice_part)->id, ['class' => 'custom-select']) }}
                        </div>

                        <div class="form-group">
                            <label for="user_roles" class="label-optional">Roles</label><br>
                            <div class="row">
                            @foreach($roles as $role)
                                @if($role->name === 'User')
                                    <input type="hidden" name="user_roles[]" id="user_roles_{{ $role->id }}" value="{{ $role->id }}">
                                    @continue
                                @endif
                                <div class="col-md-6">
                                    <x-inputs.checkbox :label="$role->name" :id="'user_roles_'.$role->id" name="user_roles[]" :value="$role->id" :checked="$singer->user->roles->contains($role)"></x-inputs.checkbox>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('singers.show', [$singer]) }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>

        </div>
    </div>

    {{ Form::close() }}

@endsection