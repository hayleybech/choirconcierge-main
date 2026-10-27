import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import DateCalendarInput from './DateCalendarInput';

vi.mock('@inertiajs/react', () => ({
	usePage: () => ({ props: { user: {} } }),
}));

vi.mock('@headlessui/react', async () => {
	const React = await vi.importActual('react');
	const Wrapper = ({ as: Component = 'div', children, ...props }) => React.createElement(Component, props, children);

	return {
		Popover: Object.assign(Wrapper, { Button: Wrapper, Panel: Wrapper }),
		Transition: ({ children }) => React.createElement(React.Fragment, null, children),
	};
});

vi.mock('react-day-picker', async () => {
	const React = await vi.importActual('react');
	return {
		DayPicker: ({ mode, selected, startMonth, endMonth }) =>
			React.createElement('div', {
				'data-testid': 'day-picker',
				'data-mode': mode,
				'data-selected': selected?.toISOString(),
				'data-start-month': startMonth?.toISOString(),
				'data-end-month': endMonth?.toISOString(),
			}),
	};
});

vi.mock('react-day-picker/style.css', () => ({}));

describe('DateCalendarInput', () => {
	it('passes the date-only value as the selected local calendar day in single mode', () => {
		const expectedDate = new Date(2025, 0, 15);

		render(
			<DateCalendarInput
				name="date"
				value="2025-01-15"
				startMonth={new Date(new Date().getFullYear(), 0)}
				endMonth={new Date(new Date().getFullYear() + 5, 11)}
				updateFn={vi.fn()}
			/>
		);

		const dayPicker = screen.getByTestId('day-picker');
		expect(dayPicker.getAttribute('data-mode')).toBe('single');
		expect(dayPicker.getAttribute('data-selected')).toBe(expectedDate.toISOString());
		expect(dayPicker.getAttribute('data-start-month')).toBe(new Date(new Date().getFullYear(), 0).toISOString());
		expect(dayPicker.getAttribute('data-end-month')).toBe(new Date(new Date().getFullYear() + 5, 11).toISOString());
	});
});