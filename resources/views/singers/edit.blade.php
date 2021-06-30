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
                                <x-inputs.text label="First Name" id="first_name" name="first_name" :value="old('first_name', $singer->first_name)" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-inputs.text label="Last Name" id="last_name" name="last_name" :value="old('last_name', $singer->last_name)" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <x-inputs.file label="Profile Picture" id="avatar" name="avatar" />
                    </div>

                    <p>
                        <x-inputs.text label="Email Address" id="email" name="email" type="email" :value="old('email', $singer->email)" />
                    </p>

                    <p>
                        <x-inputs.text label="Change Password" id="password" name="password" type="password" />
                    </p>
                    <p>
                        <x-inputs.text label="Confirm Password" id="password_confirmation" name="password_confirmation" type="password" />
                    </p>

                    <div class="form-group">
                        <x-inputs.select
                            label="Voice Part"
                            id="voice_part_id"
                            name="voice_part_id"
                            :options="$voice_parts"
                            :selected="(int) old('voice_part_id', $singer?->voice_part?->id)"
                        />
                    </div>

                    @if(Auth::user()->can('create', \App\Models\Profile::class))
                        <div class="form-group">
                            <single-date-input
                                label="Joined"
                                input-name="joined_at_input"
                                output-name="joined_at"
                                init-value="{{ old('joined_at', $singer->joined_at) }}"
                            />
                        </div>

                        <fieldset class="form-group">
                            <legend class="col-form-label">Onboarding</legend>

                            <x-inputs.radio
                                id="onboarding_enabled_yes"
                                name="onboarding_enabled"
                                value="1"
                                :checked="radio_old('onboarding_enabled', '1', '1', $singer->onboarding_enabled)"
                            >
                                <x-slot name="label">
                                    Enabled
                                    <small class="text-muted ml-2">
                                        Choose this option for new/prospective singers.
                                    </small>
                                </x-slot>
                            </x-inputs.radio>

                            <x-inputs.radio
                                id="onboarding_enabled_no"
                                name="onboarding_enabled"
                                value="0"
                                :checked="radio_old('onboarding_enabled', '0', '1', $singer->onboarding_enabled)"
                            >
                                <x-slot name="label">
                                    Disabled
                                    <small class="text-muted ml-2">
                                        Choose this option when you're adding existing singers.
                                    </small>
                                </x-slot>
                            </x-inputs.radio>
                        </fieldset>

                        <div class="form-group">
                            <label for="user_roles" class="label-optional">Roles</label><br>
                            <div class="row">
                            @foreach($roles as $role)
                                @if($role->name === 'User')
                                    <input type="hidden" name="user_roles[]" id="user_roles_{{ $role->id }}" value="{{ $role->id }}">
                                    @continue
                                @endif
                                <div class="col-md-6">
                                    <x-inputs.checkbox
                                        :label="$role->name"
                                        :id="'user_roles_'.$role->id"
                                        name="user_roles[]"
                                        :value="$role->id"
                                        :checked="checkbox_group_old('user_roles', (string) $role->id, $singer->user->roles->pluck('id')->all())"
                                    />
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