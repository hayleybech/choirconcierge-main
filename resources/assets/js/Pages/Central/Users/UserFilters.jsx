import React from 'react';
import Filters from '../../../components/Filters';
 
const UserFilters = ({ form, filters }) => (
	<Filters
		routeName="central.users.index"
		form={form}
		filters={filters}
	/>
);

export default UserFilters;
