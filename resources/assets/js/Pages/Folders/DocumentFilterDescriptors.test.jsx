import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { createDocumentFilterDescriptors } from './DocumentFilterDescriptors';

it('renders ensemble filter options with purple badges', () => {
	const ensembleFilter = createDocumentFilterDescriptors([{ id: 1, name: 'Choir' }], 2).find(
		filter => filter.name === 'ensembles.id'
	);

	render(ensembleFilter.renderOption(ensembleFilter.options[0]));

	expect(screen.getByText('Choir')).toHaveClass('bg-purple-100', 'text-purple-800');
});
