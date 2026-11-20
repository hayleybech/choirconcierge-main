import React from 'react';
import Filters from "../Filters";
 
const EventFilters = ({ filters, form }) => (
	<Filters
		routeName="events.index"
		form={form}
		filters={filters}
	/>
);

export default EventFilters;