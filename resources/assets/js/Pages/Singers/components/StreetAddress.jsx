import { countries } from 'countries-list';
import React from 'react';

const StreetAddress = ({ line1, line2, suburb, countryCode, state, postcode }) => {
	if (!line1) {
		return 'No address';
	}

	const lines = [
		line1,
		line2,
		suburb,
		[state, postcode].filter(item => item).join(' '),
		countries[countryCode]?.name,
	].filter(line => line);

	return lines.map(line => (
		<>
			{line}
			<br />
		</>
	));
};

export default StreetAddress;
