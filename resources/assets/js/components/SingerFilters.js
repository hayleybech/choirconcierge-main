import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";

const SingerFilters = ({ statuses, defaultStatus, voiceParts, roles}) => (
    <Filters
        routeName="singers.index"
        fields={(params) => ({
            'user.name': params.get('filter[user.name]') ?? '',
            'category.id': params.has('filter[category.id][]')
                ? params.getAll('filter[category.id][]').map(value => parseInt(value)) :
                [defaultStatus],
            'voice_part.id': params.getAll('filter[voice_part.id][]').map(value => parseInt(value)),
            'roles.id': params.getAll('filter[roles.id][]').map(value => parseInt(value)),
        })}
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
                    name="voice_part.id"
                    options={voiceParts.map((part) => ({ id: part.id, name: part.title }))}
                    value={data['voice_part.id']}
                    updateFn={value => setData('voice_part.id', value)}
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
        </>)}
    />
);

export default SingerFilters;