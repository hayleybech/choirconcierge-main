import React from 'react';
import Filters from "../../components/Filters";
 
const AttendanceReportFilters = ({ form, filters }) => (
    <Filters
        routeName="events.reports.attendance"
        form={form}
        filters={filters}
    />
);

export default AttendanceReportFilters;