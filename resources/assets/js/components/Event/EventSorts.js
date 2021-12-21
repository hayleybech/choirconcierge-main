import React from 'react';
import Sorts from "../Sorts";

const EventSorts = ({ }) => (
    <Sorts
        routeName="events.index"
        options={[
            { id: 'title', name: 'Title', default: true },
            { id: 'start_date', name: 'Event Date' },
            { id: 'type-title', name: 'Type' },
            { id: 'created_at', name: 'Dated Created' },
        ]}
    />
);

export default EventSorts;