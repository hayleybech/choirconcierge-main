import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import EnsembleBadge from './EnsembleBadge';
import EnsembleBadges from './EnsembleBadges';
import { createEventFilterDescriptors } from './Event/EventFilterDescriptors';
import { createPollFilterDescriptors } from './PollFilterDescriptors';
import { createSingerEnsembleFilters } from './SingerFilterDescriptors';
import { createSongFilterDescriptors } from './Song/SongFilterDescriptors';

it('shares ensemble option styling across filter descriptors and ensemble badges', () => {
	const ensembles = [
		{ id: 1, name: 'Choir' },
		{ id: 2, name: 'Youth' },
	];
	const filters = [
		createSingerEnsembleFilters(ensembles)[0],
		createEventFilterDescriptors({ eventTypes: [], ensembles, userEnsemblesCount: 2 }).find(
			filter => filter.name === 'ensembles.id'
		),
		createPollFilterDescriptors({ ensembles, showEnsembleColumn: true }).find(filter => filter.name === 'ensembles.id'),
		createSongFilterDescriptors({
			statuses: [],
			defaultStatuses: [],
			categories: [],
			userEnsemblesCount: 2,
			ensembles,
			showForProspectsDefault: [false, true],
		}).find(filter => filter.name === 'ensembles.id'),
	];
	const optionDisplays = filters.map(filter => filter.renderOption(ensembles[0], true));

	expect(optionDisplays.map(optionDisplay => optionDisplay.type)).toEqual(Array(4).fill(EnsembleBadge));

	render(
		<>
			{optionDisplays.map((optionDisplay, index) => (
				<React.Fragment key={index}>{optionDisplay}</React.Fragment>
			))}
			<EnsembleBadges ensembles={[ensembles[0]]} badgeClassName="truncate" />
			<EnsembleBadge ensemble={ensembles[0]} />
		</>
	);

	const ensembleBadges = screen.getAllByText('Choir').map(option => option.closest('span'));

	expect(ensembleBadges).toHaveLength(6);
	ensembleBadges.slice(0, 4).forEach(badge => {
		expect(badge).toHaveClass('bg-purple-100', 'text-purple-800', 'px-1', 'py-0');
	});
	expect(ensembleBadges[4]).toHaveClass('px-2', 'py-0.5');
	expect(ensembleBadges[4]).toHaveClass('truncate');
	expect(ensembleBadges[5]).toHaveClass('px-2', 'py-0.5');
});