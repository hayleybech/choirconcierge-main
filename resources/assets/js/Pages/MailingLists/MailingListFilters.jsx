import React from 'react';
import Filters from '../../components/Filters';
 
const MailingListFilters = ({ form, filters }) => (
	<Filters
		routeName="groups.index"
		form={form}
		filters={filters}
	/>
);

export default MailingListFilters;