import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm, usePage } from '@inertiajs/react';
import RenewFeesDialog from './RenewFeesDialog';

jest.mock('@inertiajs/react', () => ({
	useForm: jest.fn(),
	usePage: jest.fn(),
}));

jest.mock('../../../hooks/useRoute', () => () => ({ route: name => name }));

jest.mock('../../../components/Dialog', () => {
	const React = require('react');
	return ({ isOpen, title, okLabel, onOk, children }) =>
		isOpen ? (
			<div>
				<h1>{title}</h1>
				{children}
				<button type="button" onClick={onOk}>
					{okLabel}
				</button>
			</div>
		) : null;
});

jest.mock('../../../components/Form', () => {
	const React = require('react');
	return ({ children, onSubmit }) => <form onSubmit={onSubmit}>{children}</form>;
});

jest.mock('../../../components/inputs/DateTextInput', () => {
	const React = require('react');
	return ({ name, value }) => React.createElement('input', { name, value, readOnly: true });
});

jest.mock('../../../components/inputs/RadioGroup', () => ({ label }) => <div>{label}</div>);

const mockedUseForm = useForm;
const mockedUsePage = usePage;
const mockPost = jest.fn();

beforeEach(() => {
	jest.clearAllMocks();
	mockedUsePage.mockReturnValue({ props: { tenant: { fee_freq: 'year' } } });
	mockedUseForm.mockReturnValue({
		data: { freq: 'year', paid_until: null },
		setData: jest.fn(),
		put: jest.fn(),
		post: mockPost,
		errors: {},
		transform: jest.fn(),
	});
});

it('submits the selected singers through the bulk renewal action', () => {
	const setIsOpen = jest.fn();
	const onSuccess = jest.fn();

	render(
		<RenewFeesDialog
			isOpen
			setIsOpen={setIsOpen}
			singerIds={[4, 9]}
			onSuccess={onSuccess}
		/>
	);

	expect(screen.getByText('Mark fees as paid for 2 singers')).toBeTruthy();
	expect(screen.queryByLabelText('Membership expires')).toBeNull();

	fireEvent.click(screen.getByRole('button', { name: 'Renew Fees' }));

	expect(mockPost).toHaveBeenCalledWith(
		'singers.bulk-fees.update',
		expect.objectContaining({ preserveScroll: true, onSuccess: expect.any(Function) })
	);
});
