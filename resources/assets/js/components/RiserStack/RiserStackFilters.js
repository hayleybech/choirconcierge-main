import React from 'react';
import CheckboxGroup from "../inputs/CheckboxGroup";
import Filters from "../Filters";
import FilterActions from "../inputs/FilterActions";
import Label from "../inputs/Label";
import TextInput from "../inputs/TextInput";

const RiserStackFilters = ({ ensembles, userEnsemblesCount, form }) => (
    <Filters
        routeName="stacks.index"
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Search" forInput="search" />
                <TextInput
                    name="search"
                    value={data.search || ''}
                    updateFn={value => setData('search', value)}
                    placeholder="Search by title..."
                />
            </div>
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
        </>)}
    />
);

export default RiserStackFilters;
