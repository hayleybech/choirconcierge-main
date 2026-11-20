import React, { useState } from 'react';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import LearningStatusFilters from './LearningStatusFilters';
import RsvpFilters from './RsvpFilters';
import { createLearningStatusFilterDescriptors } from './LearningStatusFilterDescriptors';
import { createRsvpFilterDescriptors } from './RsvpFilterDescriptors';

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(() => true),
}));

vi.mock('../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

const singerStatuses = [
	{ id: 'members', name: 'Members' },
	{ id: 'prospects', name: 'Prospects' },
];
const voiceParts = [{ id: 'soprano', title: 'Soprano', colour: 'rose' }];
const ensembles = [
	{ id: 'adult', name: 'Adult' },
	{ id: 'youth', name: 'Youth' },
];
const rsvpFilters = createRsvpFilterDescriptors({ voiceParts, ensembles, singerStatuses });
const learningStatusFilters = createLearningStatusFilterDescriptors({ voiceParts, ensembles, singerStatuses });

const RsvpFilterForm = ({ setData }) => {
	const [data, updateData] = useState({
		'user.name': '',
		'status.id': ['members'],
		'enrolments.voice_part_id': [],
		'enrolments.ensemble_id': [],
		'rsvp.response': [],
	});

	return (
		<RsvpFilters
			event={{ id: 1 }}
			filters={rsvpFilters}
			form={{
				data,
				setData: (name, value) => {
					setData(name, value);
					updateData(previousData => ({ ...previousData, [name]: value }));
				},
				submit: vi.fn(),
			}}
		/>
	);
};

const LearningStatusFilterForm = ({ setData }) => {
	const [data, updateData] = useState({
		'user.name': '',
		'status.id': ['members'],
		'enrolments.voice_part_id': [],
		'learning.status': [],
		'enrolments.ensemble_id': [],
	});

	return (
		<LearningStatusFilters
			song={{ id: 1 }}
			filters={learningStatusFilters}
			form={{
				data,
				setData: (name, value) => {
					setData(name, value);
					updateData(previousData => ({ ...previousData, [name]: value }));
				},
				submit: vi.fn(),
			}}
		/>
	);
};

it('reuses singer displays and reset behavior in RSVP filters', async () => {
	const setData = vi.fn();
	render(<RsvpFilterForm setData={setData} />);
	const user = userEvent.setup();
	const statusField = screen.getByText('Singer Status').closest('fieldset');

	expect(within(statusField).getByText('1 selected')).toBeInTheDocument();
	expect(within(statusField).getByText('Default')).toBeInTheDocument();
	expect(screen.getByLabelText('Members').labels[0].querySelector('.fa-circle')).toHaveClass('text-emerald-500');

	await user.click(screen.getByLabelText('Soprano'));
	expect(screen.getByLabelText(/Soprano/).labels[0].querySelector('[class*="bg-rose-500/75"]')).toBeInTheDocument();
	await user.click(within(statusField).getByRole('button', { name: 'Clear' }));
	expect(setData).toHaveBeenCalledWith('status.id', ['']);
	await user.click(within(statusField).getByRole('button', { name: 'Reset Singer Status filter' }));
	expect(setData).toHaveBeenCalledWith('status.id', ['members']);
	expect(screen.getByLabelText('Members')).toBeChecked();

	const responseField = screen.getByText('RSVP Response').closest('fieldset');
	expect(within(responseField).getByText('Default')).toBeInTheDocument();
	await user.click(screen.getByLabelText('Going'));
	expect(screen.getByLabelText(/Going/).labels[0].querySelector('.text-emerald-500')).toBeInTheDocument();
});

it('renders Learning Status options with their shared status and voice-part descriptors', async () => {
	const setData = vi.fn();
	render(<LearningStatusFilterForm setData={setData} />);
	const user = userEvent.setup();
	const learningField = screen.getByText('Learning Status').closest('fieldset');

	expect(within(learningField).getByText('0 selected')).toBeInTheDocument();
	expect(within(learningField).getByText('Default')).toBeInTheDocument();
	await user.click(screen.getByLabelText('Assessment Ready'));

	expect(setData).toHaveBeenCalledWith('learning.status', ['assessment-ready']);
	expect(screen.getByLabelText(/Assessment Ready/).labels[0].querySelector('.fa-check')).toHaveClass('text-amber-500');
	expect(within(learningField).getByText('Modified')).toBeInTheDocument();
	await user.click(within(learningField).getByRole('button', { name: 'Reset Learning Status filter' }));
	expect(screen.getByLabelText('Assessment Ready')).not.toBeChecked();
	expect(within(learningField).getByText('Default')).toBeInTheDocument();

	const statusField = screen.getByText('Singer Status').closest('fieldset');
	expect(within(statusField).getByText('1 selected')).toBeInTheDocument();
	expect(screen.getByLabelText('Soprano').labels[0].querySelector('[class*="bg-rose-500/75"]')).toBeInTheDocument();
});