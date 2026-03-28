import React from 'react';
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";
import CheckboxGroup from "../inputs/CheckboxGroup";
import Filters from "../Filters";
import FilterActions from "../inputs/FilterActions";

const SongFilters = ({ statuses, categories, ensembles, userEnsemblesCount, form, showForProspectsDefault }) => (
    <Filters
        routeName="songs.index"
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Title" forInput="title" />
                <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} />
            </div>

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Status</legend>
                    <FilterActions
                        onSelectAll={() => setData('status.id', statuses.map(status => status.id))}
                        onClear={() => setData('status.id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="status.id"
                    options={statuses.map((status) => ({ id: status.id, name: status.title }))}
                    value={data['status.id']}
                    updateFn={value => setData('status.id', value)}
                />
            </fieldset>

            {userEnsemblesCount > 1 && (
                <fieldset>
                    <div className="flex items-center justify-between">
                        <legend className="text-sm font-medium text-gray-700">Ensemble</legend>
                        <FilterActions
                            onSelectAll={() => setData('ensembles.id', ensembles.map(ensemble => ensemble.id))}
                            onClear={() => setData('ensembles.id', [])}
                        />
                    </div>
                    <CheckboxGroup
                        name="ensembles.id"
                        options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
                        value={data['ensembles.id']}
                        updateFn={value => setData('ensembles.id', value)}
                    />
                </fieldset>
            )}

            {showForProspectsDefault.length > 1 && (
                <fieldset>
                    <div className="flex items-center justify-between">
                        <legend className="text-sm font-medium text-gray-700">Audition Songs</legend>
                        <FilterActions
                            onSelectAll={() => setData('show_for_prospects', [false, true])}
                            onClear={() => setData('show_for_prospects', [])}
                        />
                    </div>
                    <CheckboxGroup
                        name="show_for_prospects"
                        options={[{ id: false, name: 'Non-Audition Songs' }, { id: true, name: 'Audition Songs' }]}
                        value={data.show_for_prospects}
                        updateFn={value => setData('show_for_prospects', value)}
                    />
                </fieldset>
            )}

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Category</legend>
                    <FilterActions
                        onSelectAll={() => setData('categories.id', categories.map(category => category.id))}
                        onClear={() => setData('categories.id', [])}
                    />
                </div>
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