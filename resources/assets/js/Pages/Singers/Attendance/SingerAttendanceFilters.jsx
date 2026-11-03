import React from 'react';
import CheckboxGroup from "../../../components/inputs/CheckboxGroup";
import Filters from "../../../components/Filters";
import DateRangeCalendarInput from "../../../components/inputs/DateRangeCalendarInput";
import FilterActions from "../../../components/inputs/FilterActions";
import { DateTime } from "luxon";
 
const SingerAttendanceFilters = ({ eventTypes, form, singer }) => (
    <Filters
        routeName="singers.attendance"
        routeParams={{ singer }}
        form={form}
        render={(data, setData) => (<>
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
                    value={data['type.id'] || []}
                    updateFn={value => setData('type.id', value)}
                />
            </fieldset>

            <fieldset className="relative z-[2]">
                <legend className="text-sm font-medium text-gray-700">Date Range</legend>
                <DateRangeCalendarInput
                  ariaLabel="Attendance date range"
                  startDate={data['starts_after']}
                  endDate={data['starts_before']}
                  updateFn={range => {
                      setData('starts_after', range?.from ? DateTime.fromJSDate(range.from).toISODate() : '');
                      setData('starts_before', range?.to ? DateTime.fromJSDate(range.to).toISODate() : '');
                  }}
                />
            </fieldset>
        </>)}
    />
);

export default SingerAttendanceFilters;
