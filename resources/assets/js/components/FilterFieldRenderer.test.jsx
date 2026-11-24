import React from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterFieldRenderer from './FilterFieldRenderer';

const ensembleFilter = {
	name: 'ensembles.id',
	label: 'Ensemble',
	type: 'choice',
	multiple: true,
	defaultValue: [],
	options: [
		{ id: 1, name: 'Choir' },
		{ id: 2, name: 'Youth' },
	],
	allValuesLabel: 'All',
	showActions: true,
	showDefaultMarker: true,
};

it('renders styled multi-choice options and spacious actions in the drawer variant', () => {
	const onChange = vi.fn();
	const { container } = render(
		<FilterFieldRenderer filter={ensembleFilter} value={[1]} onChange={onChange} variant="drawer" />
	);

	expect(screen.getByText('Ensemble')).toBeInTheDocument();
	expect(screen.getByText('1 selected')).toBeInTheDocument();
	expect(screen.queryByText('Modified')).not.toBeInTheDocument();
	expect(container.querySelectorAll('.fa-asterisk')).toHaveLength(2);
	expect(container.querySelectorAll('.fa-asterisk')[0]).toHaveClass('text-purple-700');
	expect(screen.getByText('Choir').closest('span')).toHaveClass('bg-gray-100');
	expect(container.querySelector('.grid-cols-2')).toBeInTheDocument();

	fireEvent.click(screen.getByLabelText('Youth'));

	expect(onChange).toHaveBeenCalledWith([1, 2]);

	fireEvent.click(screen.getByRole('button', { name: 'Select All' }));
	expect(onChange).toHaveBeenLastCalledWith([1, 2]);

	fireEvent.click(screen.getByRole('button', { name: 'Clear' }));
	expect(onChange).toHaveBeenLastCalledWith([]);

	fireEvent.click(screen.getByRole('button', { name: 'Reset Ensemble filter' }));
	expect(onChange).toHaveBeenLastCalledWith([]);
});

it('renders the same option adapter in the compact toolbar option panel', () => {
	const onChange = vi.fn();
	const { container } = render(
		<FilterFieldRenderer filter={ensembleFilter} value={[]} onChange={onChange} variant="toolbar" />
	);

	const optionPanel = container.querySelector('[data-filter-field-options]');
	expect(optionPanel).toHaveClass('max-h-60');
	expect(
		within(optionPanel)
			.getByText('Choir')
			.closest('span')
	).toHaveClass('bg-gray-100');

	fireEvent.click(within(optionPanel).getByLabelText('Choir'));

	expect(onChange).toHaveBeenCalledWith([1]);
});

it('marks drawer options that differ from their descriptor defaults', () => {
	const filter = { ...ensembleFilter, defaultValue: [1] };
	const { container } = render(
		<FilterFieldRenderer filter={filter} value={[]} onChange={vi.fn()} variant="drawer" />
	);

	expect(container.querySelectorAll('.fa-asterisk')).toHaveLength(2);
	expect(container.querySelectorAll('.fa-asterisk')[0]).toHaveClass('text-purple-700');
});

it('adds the default marker centrally around custom drawer option displays', () => {
	const renderOption = vi.fn(option => <span>{option.name}</span>);
	const filter = { ...ensembleFilter, defaultValue: [1], renderOption };

	render(<FilterFieldRenderer filter={filter} value={[]} onChange={vi.fn()} variant="drawer" />);

	expect(renderOption).toHaveBeenCalledWith(expect.objectContaining({ id: 1 }), false, true);
	expect(screen.getByText('Choir')).toBeInTheDocument();
	expect(screen.getAllByTitle('Filter value differs from default')).toHaveLength(1);
	expect(screen.getByTitle('Filter value differs from default').querySelector('.fa-asterisk')).toHaveClass(
		'text-purple-700'
	);
});

it('uses a descriptive reset label for text drawer fields', () => {
	const filter = {
		name: 'search',
		label: 'Search',
		type: 'text',
		defaultValue: '',
		showActions: true,
		showDefaultMarker: true,
	};
	const onChange = vi.fn();

	render(<FilterFieldRenderer filter={filter} value="Soprano" onChange={onChange} variant="drawer" />);

	fireEvent.click(screen.getByRole('button', { name: 'Reset Search filter' }));
	expect(onChange).toHaveBeenCalledWith('');
});

it('allows toolbar single-choice values to be toggled off', () => {
	const onChange = vi.fn();
	const filter = {
		name: 'status',
		label: 'Status',
		type: 'choice',
		options: [
			{ id: 'active', name: 'Active' },
			{ id: 'inactive', name: 'Inactive' },
		],
	};

	render(
		<FilterFieldRenderer filter={filter} value="active" onChange={onChange} variant="toolbar" layout="options" />
	);

	fireEvent.click(screen.getByLabelText('Active'));
	fireEvent.click(screen.getByLabelText('Inactive'));

	expect(onChange.mock.calls).toEqual([[''], ['inactive']]);
});
