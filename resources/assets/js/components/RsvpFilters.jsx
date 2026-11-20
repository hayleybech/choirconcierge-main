import React from 'react';
import Filters from './Filters';
 
const RsvpFilters = ({ event, form, filters }) => (
    <Filters
        routeName="events.rsvps.index"
        routeParams={{ event: event.id }}
        form={form}
        filters={filters}
    />
);

export default RsvpFilters;
