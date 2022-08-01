import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import CheckboxGroup from "../inputs/CheckboxGroup";
import Filters from "../Filters";

const SongFilters = ({ statuses, defaultStatuses, categories, showForProspectsDefault }) => (
    <Filters
        routeName="songs.index"
        fields={[
            { name: 'title', defaultValue: '' },
            { name: 'status.id', multiple: true, defaultValue: defaultStatuses },
            { name: 'categories.id', multiple: true },
            { name: 'show_for_prospects', multiple: true, multipleBool: true, defaultValue: showForProspectsDefault },
        ]}
        render={(data, setData) => (<>
            <div>
                <Label label="Title" forInput="title" />
                <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} />
            </div>

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Status</legend>
                <CheckboxGroup
                    name="status.id"
                    options={statuses.map((status) => ({ id: status.id, name: status.title }))}
                    value={data['status.id']}
                    updateFn={value => setData('status.id', value)}
                />
            </fieldset>

            {showForProspectsDefault.length > 1 && (
                <fieldset>
                    <legend className="text-sm font-medium text-gray-700">Audition Songs</legend>
                    <CheckboxGroup
                        name="show_for_prospects"
                        options={[{ id: false, name: 'Non-Audition Songs' }, { id: true, name: 'Audition Songs' }]}
                        value={data.show_for_prospects}
                        updateFn={value => setData('show_for_prospects', value)}
                    />
                </fieldset>
            )}

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Category</legend>
                <CheckboxGroup
                    name="categories.id"
                    options={categories.map((category) => ({ id: category.id, name: category.title }))}
                    value={data['categories.id']}
                    updateFn={value => setData('categories.id', value)}
                />
            </fieldset>
        </>)}
    />
);

export default SongFilters;