import React from 'react';
import Filters from '../../components/Filters';
 
const DocumentFilters = ({ form, filters }) => (
	<Filters
		routeName="folders.index"
		form={form}
		filters={filters}
	/>
);

export default DocumentFilters;
