import React from 'react';
import Filters from './Filters';
 
const PollFilters = ({ form, filters }) => (
	<Filters
		routeName="polls.index"
		form={form}
		filters={filters}
	/>
);

export default PollFilters;
