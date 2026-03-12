import React from 'react';
import Filters from "./Filters";
import Label from "./inputs/Label";
import TextInput from "./inputs/TextInput";
import CheckboxGroup from "./inputs/CheckboxGroup";

const AttendanceFilters = ({ event, voiceParts, form, ensembles, singerCategories }) => (
    <Filters
        routeName="events.attendances.index"
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
                <legend className="text-sm font-medium text-gray-700">Attendance Response</legend>
                <CheckboxGroup
                    name="attendance.response"
                    options={[
                        { id: 'present', name: 'On Time' },
                        { id: 'late', name: 'Late' },
                        { id: 'unknown', name: 'Not recorded' },
                        { id: 'late_deemed_absent', name: 'Late (Deemed Absent)' },
                        { id: 'absent', name: 'Absent' },
                        { id: 'absent_apology', name: 'Absent (With Apology)' },
                    ]}
                    value={data['attendance.response']}
                    updateFn={value => setData('attendance.response', value)}
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

export default AttendanceFilters;
