import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import { Settings } from 'luxon';
import DateRangeCalendarInput from './DateRangeCalendarInput';

const originalLocale = Settings.defaultLocale;

vi.mock('@inertiajs/react', () => ({
	usePage: () => ({ props: { user: {} } }),
}));

vi.mock('@headlessui/react', async () => {
	const React = await vi.importActual('react');
	const Wrapper = ({ as: Component = 'div', children, ...props }) =>
		React.createElement(Component, props, typeof children === 'function' ? children({ close: vi.fn(), open: true }) : children);

	return {
		Popover: Object.assign(Wrapper, { Button: Wrapper, Panel: Wrapper }),
		Transition: ({ children }) => React.createElement(React.Fragment, null, children),
	};
});

vi.mock('react-day-picker', async () => {
	const React = await vi.importActual('react');
	return {
		DayPicker: ({ mode, onSelect }) =>
			React.createElement(
				'div',
				{ 'data-testid': 'day-picker', 'data-mode': mode },
				React.createElement(
					'button',
					{ type: 'button', onClick: () => onSelect({ from: new Date(2025, 2, 1), to: new Date(2025, 2, 31) }) },
					'Choose range',
				),
			),
	};
});

vi.mock('react-day-picker/style.css', () => ({}));

describe('DateRangeCalendarInput', () => {
	beforeAll(() => {
		Settings.defaultLocale = 'en-GB';
	});

	afterAll(() => {
		Settings.defaultLocale = originalLocale;
	});

	it('renders one range calendar, displays the selected date bounds, and renders children', () => {
		render(
			<DateRangeCalendarInput
				startDate="2025-01-15"
				endDate="2025-02-20"
				startMonth={new Date(2017, 0)}
				endMonth={new Date(2030, 11)}
				updateFn={vi.fn()}
			>
				<button type="button">Last 7 days</button>
			</DateRangeCalendarInput>
		);

		expect(screen.getByTestId('day-picker').getAttribute('data-mode')).toBe('range');
		expect(screen.getByRole('button', { name: 'Last 7 days' })).toBeTruthy();
		expect(screen.getByRole('textbox', { name: 'Attendance report date range' }).value).toContain('15 Jan 2025');
		expect(screen.getByRole('textbox', { name: 'Attendance report date range' }).value).toContain('20 Feb 2025');
		expect(screen.getByLabelText('Selected date range').textContent).toBe('15 Jan 2025 – 20 Feb 2025');
		expect(screen.getByRole('textbox', { name: 'Attendance report date range' }).className).toContain('bg-gray-100');
	});

	it('does not update the applied range until Apply is pressed', () => {
		const updateFn = vi.fn();

		render(
			<DateRangeCalendarInput
				startDate="2025-01-15"
				endDate="2025-02-20"
				updateFn={updateFn}
			/>
		);

		screen.getByRole('button', { name: 'Choose range' }).click();
		expect(updateFn).not.toHaveBeenCalled();

		screen.getByRole('button', { name: 'Apply' }).click();
		expect(updateFn).toHaveBeenCalledWith({ from: new Date(2025, 2, 1), to: new Date(2025, 2, 31) });
	});
});