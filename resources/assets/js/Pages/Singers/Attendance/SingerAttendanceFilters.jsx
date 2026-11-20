import React from 'react';
import Filters from '../../../components/Filters';
 
const SingerAttendanceFilters = ({ filters, form, singer }) => (
    <Filters
        routeName="singers.attendance"
        routeParams={{ singer }}
        form={form}
        filters={filters}
    />
);

export default SingerAttendanceFilters;
