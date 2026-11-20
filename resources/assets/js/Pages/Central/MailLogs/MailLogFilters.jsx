import React from 'react';
import Filters from '../../../components/Filters';
 
const MailLogFilters = ({ form, filters }) => (
	<Filters
		routeName="central.mail-logs.index"
		form={form}
		filters={filters}
	/>
);

export default MailLogFilters;
