import React from 'react';
import CheckboxGroup from "../inputs/CheckboxGroup";
import Filters from "../Filters";

const RiserStackFilters = ({ ensembles, userEnsemblesCount, form }) => (
    <Filters
        routeName="stacks.index"
        form={form}
        render={(data, setData) => (<>
            {userEnsemblesCount > 1 && (
                <fieldset>
                    <legend className="text-sm font-medium text-gray-700">Ensemble</legend>
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
