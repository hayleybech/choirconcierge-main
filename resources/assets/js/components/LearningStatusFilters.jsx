import React from 'react';
import Filters from './Filters';
 
const LearningStatusFilters = ({ song, form, filters }) => (
    <Filters
        routeName="songs.singers.index"
        routeParams={{ song: song.id }}
        form={form}
        filters={filters}
    />
);

export default LearningStatusFilters;
