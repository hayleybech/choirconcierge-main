import React from 'react';
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import Filters from "../../components/Filters";
import FilterActions from "../../components/inputs/FilterActions";
import DateRangeCalendarInput from "../../components/inputs/DateRangeCalendarInput";
import { DateTime } from "luxon";
 
const AttendanceReportFilters = ({ eventTypes, voiceParts, form, dateRangePresets }) => (
    <Filters
        routeName="events.reports.attendance"
        form={form}
        render={(data, setData) => (<>
            <fieldset className="relative z-[2]">
                <legend className="text-sm font-medium text-gray-700">Date Range</legend>
                <DateRangeCalendarInput
                    ariaLabel="Attendance report date range"
                    startDate={data['starts_after']}
                    endDate={data['starts_before']}
                    startMonth={new Date(2017, 0)}
                    endMonth={new Date(new Date().getFullYear() + 5, 11)}
                    updateFn={range => {
                        setData('starts_after', range?.from ? DateTime.fromJSDate(range.from).toISODate() : '');
                        setData('starts_before', range?.to ? DateTime.fromJSDate(range.to).toISODate() : '');
                    }}
                >
                    {dateRangePresets}
                </DateRangeCalendarInput>
            </fieldset>
            <fieldset>
                <div className="flex items-center justify-between">
                    <legend className="text-sm font-medium text-gray-700">Event Type</legend>
                    <FilterActions
                        onSelectAll={() => setData('type.id', eventTypes.map(type => type.id))}
                        onClear={() => setData('type.id', [])}
                    />
                </div>
                <CheckboxGroup
                    name="type.id"
                    options={eventTypes.map((type) => ({ id: type.id, name: type.title }))}
                    value={data['type.id']}
                    updateFn={value => setData('type.id', value)}
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
                    options={voiceParts.map(part => ({ id: part.id, name: part.title }))}
                    value={data['enrolments.voice_part_id']}
                    updateFn={value => setData('enrolments.voice_part_id', value)}
                />
            </fieldset>

        </>)}
    />
);

export default AttendanceReportFilters;