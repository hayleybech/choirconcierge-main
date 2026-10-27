import React from 'react';
import { render, screen } from '@testing-library/react';
import EnsembleBadges from './EnsembleBadges';

it('labels items with no assigned ensembles as applying to all ensembles', () => {
	render(<EnsembleBadges ensembles={[]} />);

	expect(screen.getByText('All Ensembles')).toBeTruthy();
});

it('shows assigned ensemble names without the all-ensembles label', () => {
	render(<EnsembleBadges ensembles={[{ id: 1, name: 'Sopranos' }]} />);

	expect(screen.getByText('Sopranos')).toBeTruthy();
	expect(screen.queryByText('All Ensembles')).toBeNull();
});