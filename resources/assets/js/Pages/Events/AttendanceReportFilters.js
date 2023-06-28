import React from 'react';
import CheckboxGroup from "../../components/inputs/CheckboxGroup";
import Filters from "../../components/Filters";
import DateInput from "../../components/inputs/Date";

const AttendanceReportFilters = ({ eventTypes, form }) => (
    <Filters
        routeName="events.reports.attendance"
        form={form}
        render={(data, setData) => (<>
            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Event Type</legend>
                <CheckboxGroup
                    name="type.id"
                    options={eventTypes.map((type) => ({ id: type.id, name: type.title }))}
                    value={data['type.id']}
                    updateFn={value => setData('type.id', value)}
                />
            </fieldset>

            <fieldset>
                <legend className="text-sm font-medium text-gray-700">Events After</legend>
                <DateInput
                  name="starts_after"
                  value={data['starts_after']}
                  updateFn={value => setData('starts_after', value)}
                />
            </fieldset>

          <fieldset>
            <legend className="text-sm font-medium text-gray-700">Events Before</legend>
            <DateInput
              name="starts_before"
              value={data['starts_before']}
              updateFn={value => setData('starts_before', value)}
            />
          </fieldset>
        </>)}
    />
);

export default AttendanceReportFilters;