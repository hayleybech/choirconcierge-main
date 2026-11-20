import React from 'react';
import Filters from '../Filters';

const SongFilters = ({ filters, form }) => (
	<Filters
		routeName="songs.index"
		form={form}
		filters={filters}
	/>
);

export default SongFilters;
