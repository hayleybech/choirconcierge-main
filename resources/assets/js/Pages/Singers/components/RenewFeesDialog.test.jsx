import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useForm, usePage } from '@inertiajs/react';
import { vi } from 'vitest';
import RenewFeesDialog from './RenewFeesDialog';

vi.mock('@inertiajs/react', () => ({
	useForm: vi.fn(),
	usePage: vi.fn(),
}));

vi.mock('../../../hooks/useRoute', () => ({ default: () => ({ route: name => name }) }));

vi.mock('../../../components/Dialog', async () => {
	const React = await vi.importActual('react');
	return {
		default: ({ isOpen, title, okLabel, onOk, children }) =>
			isOpen
				? React.createElement(
						'div',
						null,
						React.createElement('h1', null, title),
						children,
						React.createElement('button', { type: 'button', onClick: onOk }, okLabel),
					)
				: null,
	};
});

vi.mock('../../../components/Form', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, onSubmit }) => React.createElement('form', { onSubmit }, children) };
});

vi.mock('../../../components/inputs/DateTextInput', async () => {
	const React = await vi.importActual('react');
	return { default: ({ name, value }) => React.createElement('input', { name, value, readOnly: true }) };
});

vi.mock('../../../components/inputs/RadioGroup', () => ({ default: ({ label }) => <div>{label}</div> }));

const mockedUseForm = useForm;
const mockedUsePage = usePage;
const mockPost = vi.hoisted(() => vi.fn());

beforeEach(() => {
	vi.clearAllMocks();
	mockedUsePage.mockReturnValue({ props: { tenant: { fee_freq: 'year' } } });
	mockedUseForm.mockReturnValue({
		data: { freq: 'year', paid_until: null },
		setData: vi.fn(),
		put: vi.fn(),
		post: mockPost,
		errors: {},
		transform: vi.fn(),
	});
});

it('submits the selected singers through the bulk renewal action', () => {
	const setIsOpen = vi.fn();
	const onSuccess = vi.fn();

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
