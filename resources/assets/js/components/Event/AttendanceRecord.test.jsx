import React from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import { vi } from 'vitest';
import AttendanceRecord from './AttendanceRecord';

const { putMock } = vi.hoisted(() => ({ putMock: vi.fn() }));

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');

	return {
		router: { put: vi.fn() },
		usePage: vi.fn(() => ({ props: { tenant: { id: 1 } } })),
		useForm: initialData => {
			const [data, setFormData] = React.useState(initialData);

			return {
				data,
				put: (url, options) => putMock(url, data, options),
				processing: false,
				setData: (keyOrData, value) =>
					setFormData(currentData =>
						typeof keyOrData === 'string'
							? { ...currentData, [keyOrData]: value }
							: keyOrData
					),
				transform: () => {},
			};
		},
		Link: React.forwardRef(({ children, ...props }, ref) =>
			React.createElement('a', { ref, ...props }, children)
		),
	};
});

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: routeName => routeName }) }));

describe('AttendanceRecord', () => {
	beforeEach(() => {
		putMock.mockClear();
	});

	it('edits the status and absence reason in a mobile modal', async () => {
		render(
			<AttendanceRecord
				attendance={{ response: 'absent', absent_reason: '' }}
				singerId={7}
				event={42}
				isMobile
			/>
		);

		fireEvent.click(screen.getByRole('button', { name: /Absent/ }));
		const dialog = await screen.findByRole('dialog');
		expect(within(dialog).getByRole('button', { name: 'Present' })).toBeTruthy();
		fireEvent.click(within(dialog).getByRole('button', { name: 'Present' }));
		expect(within(dialog).queryByRole('textbox', { name: 'Reason for absence' })).toBeNull();
		fireEvent.click(within(dialog).getByRole('button', { name: 'Absent' }));
		fireEvent.change(within(dialog).getByRole('textbox', { name: 'Reason for absence' }), {
			target: { value: 'Family emergency' },
		});
		fireEvent.click(within(dialog).getByRole('button', { name: 'Save' }));

		expect(putMock).toHaveBeenCalledWith(
			'events.attendances.update',
			{ response: 'absent', absent_reason: 'Family emergency' },
			expect.objectContaining({ preserveScroll: true })
		);
	});

	it('shows an existing mobile absence reason in a tooltip', () => {
		render(
			<AttendanceRecord
				attendance={{ response: 'absent', absent_reason: 'Family emergency' }}
				singerId={7}
				event={42}
				isMobile
			/>
		);

		fireEvent.click(screen.getByRole('button', { name: 'Show attendance reason' }));

		expect(screen.getByRole('tooltip')).toHaveTextContent('Family emergency');
	});
});