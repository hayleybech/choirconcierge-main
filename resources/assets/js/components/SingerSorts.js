import React from 'react';
import Sorts from "./Sorts";

const SingerSorts = ({ }) => (
    <Sorts
        routeName="singers.index"
        options={[
            { id: 'full-name', name: 'Name', default: true },
            { id: 'status-title', name: 'Status' },
            { id: 'part-title', name: 'Voice Part' },
        ]}
    />
);

export default SingerSorts;