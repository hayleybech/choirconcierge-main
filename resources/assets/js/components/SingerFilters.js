import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";
import RadioGroup from "./inputs/RadioGroup";

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
                <legend className="text-sm font-medium text-gray-700">Status</legend>
                <CheckboxGroup
                    name="category.id"
                    options={statuses.map((status) => ({ id: status.id, name: status.name }))}
                    value={data['category.id']}
                    updateFn={value => setData('category.id', value)}
                />
            </fieldset>

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Voice Part</legend>
                <CheckboxGroup
                    name="enrolments.voice_part_id"
                    options={voiceParts.map((part) => ({ id: part.id, name: part.title }))}
                    value={data['enrolments.voice_part_id']}
                    updateFn={value => setData('enrolments.voice_part_id', value)}
                />
            </fieldset>

          <fieldset>
            <legend className="text-sm font-medium text-gray-700">Ensemble</legend>
            <CheckboxGroup
              name="enrolments.ensemble_id"
              options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
              value={data['enrolments.ensemble_id']}
              updateFn={value => setData('enrolments.ensemble_id', value)}
            />
          </fieldset>

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Role</legend>
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
                    label={<Label label="Fee Status" />}
                    options={[
                        { id: 'paid', name: 'Paid', icon: 'check-circle', colour: 'green-500' },
                        { id: 'expires-soon', name: 'Expires Soon', icon: 'exclamation-triangle', colour: 'orange-500' },
                        { id: 'expired', name: 'Expired', icon: 'times-circle', colour: 'red-500' },
                        { id: 'unknown', name: 'Unknown', icon: 'question-circle', colour: 'gray-500' },
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