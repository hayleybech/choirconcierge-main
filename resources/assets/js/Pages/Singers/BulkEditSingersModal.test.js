import React from 'react';
import { render, screen } from '@testing-library/react';
import { useForm } from '@inertiajs/react';
import BulkEditSingersModal from './BulkEditSingersModal';

jest.mock('@inertiajs/react', () => ({ useForm: jest.fn() }));

jest.mock('../../hooks/useRoute', () => () => ({ route: name => name }));

jest.mock('../../components/Dialog', () => {
	const React = require('react');
	return ({ isOpen, title, children }) =>
		isOpen ? (
			<div>
				<h1>{title}</h1>
				{children}
			</div>
		) : null;
});

jest.mock('../../components/inputs/RadioGroup', () => ({ label }) => <div>{label}</div>);

const mockedUseForm = useForm;

beforeEach(() => {
	mockedUseForm.mockReturnValue({
		data: { singer_ids: [4, 9], status: null },
		setData: jest.fn(),
		post: jest.fn(),
		processing: false,
		reset: jest.fn(),
		errors: {},
	});
});

it('keeps fee renewal out of the bulk status-edit dialog', () => {
	render(
		<BulkEditSingersModal
			isOpen
			setIsOpen={jest.fn()}
			selectedSingerIds={[4, 9]}
			statuses={[]}
		/>
	);

	expect(screen.getByText('Status')).toBeTruthy();
	expect(screen.queryByText('Renew membership fees')).toBeNull();
});
