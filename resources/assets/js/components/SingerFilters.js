import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";
import RadioGroup from "./inputs/RadioGroup";
import FilterActions from "./inputs/FilterActions";

const SingerFilters = ({ statuses, voiceParts, roles, form, ensembles }) => (
    <Filters
        routeName="singers.index"
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Name" forInput="user.name" />
                <TextInput name="user.name" value={data['user.name']} updateFn={value => setData('user.name', value)} />
            </div>

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Status</legend>
                    <FilterActions
                        onSelectAll={() => setData('category.id', statuses.map(status => status.id))}
                        onClear={() => setData('category.id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="category.id"
                    options={statuses.map((status) => ({ id: status.id, name: status.name }))}
                    value={data['category.id']}
                    updateFn={value => setData('category.id', value)}
                />
            </fieldset>

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Voice Part</legend>
                    <FilterActions
                        onSelectAll={() => setData('enrolments.voice_part_id', voiceParts.map(part => part.id))}
                        onClear={() => setData('enrolments.voice_part_id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="enrolments.voice_part_id"
                    options={voiceParts.map((part) => ({ id: part.id, name: part.title }))}
                    value={data['enrolments.voice_part_id']}
                    updateFn={value => setData('enrolments.voice_part_id', value)}
                />
            </fieldset>

          {ensembles.length > 1 && (
            <fieldset>
              <div className="flex items-center justify-between">
                <legend className="text-sm font-medium text-gray-700">Ensemble</legend>
                <FilterActions
                    onSelectAll={() => setData('enrolments.ensemble_id', ensembles.map(ensemble => ensemble.id))}
                    onClear={() => setData('enrolments.ensemble_id', [])}
                />
              </div>
              <CheckboxGroup
                name="enrolments.ensemble_id"
                options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
                value={data['enrolments.ensemble_id']}
                updateFn={value => setData('enrolments.ensemble_id', value)}
              />
            </fieldset>
          )}

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Role</legend>
                    <FilterActions
                        onSelectAll={() => setData('roles.id', roles.map(role => role.id))}
                        onClear={() => setData('roles.id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="roles.id"
                    options={roles.map((role) => ({ id: role.id, name: role.name }))}
                    value={data['roles.id']}
                    updateFn={value => setData('roles.id', value)}
                />
            </fieldset>

            <fieldset>
                <RadioGroup
                    name="fee_status"
                    label={
                        <div className="flex items-center justify-between">
                            <Label label="Fee Status" />
                            <FilterActions
                                onClear={() => setData('fee_status', '')}
                            />
                        </div>
                    }
                    options={[
                        { id: 'paid', name: 'Paid', icon: 'check-circle', colour: 'green-500', textColour: 'text-green-500' },
                        { id: 'expires-soon', name: 'Expires Soon', icon: 'exclamation-triangle', colour: 'orange-500', textColour: 'text-orange-500' },
                        { id: 'expired', name: 'Expired', icon: 'times-circle', colour: 'red-500', textColour: 'text-red-500' },
                        { id: 'unknown', name: 'Unknown', icon: 'question-circle', colour: 'gray-500', textColour: 'text-gray-500' },
                    ]}
                    selected={data.fee_status}
                    setSelected={value => setData('fee_status', value)}
                    vertical
                />
            </fieldset>
        </>)}
    />
);

export default SingerFilters;