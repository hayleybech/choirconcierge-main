import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";
import FilterActions from "./inputs/FilterActions";
import Badge from "./Badge";
import SingerStatus from "../SingerStatus";
import SingerStatusTag from "./SingerStatusTag";
import VoicePartTag from "./VoicePartTag";
import RsvpTag from "./Event/RsvpTag";
 
const RsvpFilters = ({ event, voiceParts, form, ensembles, singerStatuses }) => (
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
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Singer Status</legend>
                    <FilterActions
                        onSelectAll={() => setData('status.id', singerStatuses.map(status => status.id))}
                        onClear={() => setData('status.id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="status.id"
                    options={singerStatuses.map((status) => ({ id: status.id, name: status.name }))}
                    value={data['status.id']}
                    updateFn={value => setData('status.id', value)}
                    renderOption={option => (
                        <SingerStatusTag status={new SingerStatus(option.id)} withLabel />
                    )}
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
                    options={voiceParts.map((part) => ({ id: part.id, name: part.title, colour: part.colour }))}
                    value={data['enrolments.voice_part_id']}
                    updateFn={value => setData('enrolments.voice_part_id', value)}
                    renderOption={option => <VoicePartTag title={option.name} colour={option.colour} />}
                />
            </fieldset>

            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">RSVP Response</legend>
                    <FilterActions
                        onSelectAll={() => setData('rsvp.response', ['yes', 'maybe', 'no', 'unknown'])}
                        onClear={() => setData('rsvp.response', [])}
                    />
                </div>
                <CheckboxGroup
                    name="rsvp.response"
                    options={[
                        { id: 'yes', name: 'Going', icon: 'check', colour: 'emerald' },
                        { id: 'maybe', name: 'Maybe', icon: 'question', colour: 'amber' },
                        { id: 'no', name: 'Not going', icon: 'times', colour: 'red' },
                        { id: 'unknown', name: 'No RSVP', icon: 'circle', colour: 'gray' },
                    ]}
                    value={data['rsvp.response']}
                    updateFn={value => setData('rsvp.response', value)}
                    renderOption={option => (
                        <RsvpTag label={option.name} icon={option.icon} colour={option.colour} />
                    )}
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
                renderOption={option => <Badge colour="bg-purple-100 text-purple-800">{option.name}</Badge>}
              />
            </fieldset>
          )}
        </>)}
    />
);

export default RsvpFilters;
