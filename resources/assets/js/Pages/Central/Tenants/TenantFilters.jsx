import React from 'react';
import Filters from "../../../components/Filters";
 
const TenantFilters = ({ form, filters }) => (
    <Filters
        routeName="central.tenants.index"
        form={form}
        filters={filters}
    />
);

export default TenantFilters;