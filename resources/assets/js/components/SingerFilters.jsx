import React from 'react';
import Filters from './Filters';

const SingerFilters = ({ filters, form }) => (
	<Filters routeName="singers.index" form={form} filters={filters} autoSubmit={false} showClearAll={false} />
);

export default SingerFilters;