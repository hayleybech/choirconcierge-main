import React from 'react';
import { render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import { vi } from 'vitest';
import BulkEditSingersModal from './BulkEditSingersModal';

vi.mock('@inertiajs/react', () => ({ useForm: vi.fn() }));

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => name }) }));

vi.mock('../../components/Dialog', async () => {
	const React = await vi.importActual('react');
	return {
		default: ({ isOpen, title, children }) =>
			isOpen ? React.createElement('div', null, React.createElement('h1', null, title), children) : null,
	};
});

vi.mock('../../components/inputs/RadioGroup', () => ({ default: ({ label }) => <div>{label}</div> }));

const mockedUseForm = useForm;

beforeEach(() => {
	mockedUseForm.mockReturnValue({
		data: { singer_ids: [4, 9], status: null },
		setData: vi.fn(),
		post: vi.fn(),
		processing: false,
		reset: vi.fn(),
		errors: {},
	});
});

it('keeps fee renewal out of the bulk status-edit dialog', () => {
	render(
		<BulkEditSingersModal
			isOpen
			setIsOpen={vi.fn()}
			selectedSingerIds={[4, 9]}
			statuses={[]}
		/>
	);

	expect(screen.getByText('Status')).toBeTruthy();
	expect(screen.queryByText('Renew membership fees')).toBeNull();
});
