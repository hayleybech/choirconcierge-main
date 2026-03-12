import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";

const RsvpFilters = ({ event, voiceParts, form, ensembles, singerCategories }) => (
    <Filters
        routeName="events.rsvps.index"
        routeParams={{ event: event.id }}
        form={form}
        render={(data, setData) => (<>
            <div>
                <Label label="Name" forInput="user.name" />
                <TextInput name="user.name" value={data['user.name']} updateFn={value => setData('user.name', value)} />
            </div>

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Singer Category</legend>
                <CheckboxGroup
                    name="category.id"
                    options={singerCategories.map((category) => ({ id: category.id, name: category.name }))}
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
                <legend className="text-sm font-medium text-gray-700">RSVP Response</legend>
                <CheckboxGroup
                    name="rsvp.response"
                    options={[
                        { id: 'yes', name: 'Going' },
                        { id: 'maybe', name: 'Maybe' },
                        { id: 'no', name: 'Not going' },
                        { id: 'unknown', name: 'No RSVP' },
                    ]}
                    value={data['rsvp.response']}
                    updateFn={value => setData('rsvp.response', value)}
                />
            </fieldset>

          {ensembles.length > 1 && (
            <fieldset>
              <legend className="text-sm font-medium text-gray-700">Ensemble</legend>
              <CheckboxGroup
                name="enrolments.ensemble_id"
                options={ensembles.map((ensemble) => ({ id: ensemble.id, name: ensemble.name }))}
                value={data['enrolments.ensemble_id']}
                updateFn={value => setData('enrolments.ensemble_id', value)}
              />
            </fieldset>
          )}
        </>)}
    />
);

export default RsvpFilters;
