import React from 'react';
import Filters from './Filters';
 
const AttendanceFilters = ({ event, form, filters }) => (
	<Filters
		routeName="events.attendances.index"
		routeParams={{ event: event.id }}
		form={form}
		filters={filters}
	/>
);

export default AttendanceFilters;
