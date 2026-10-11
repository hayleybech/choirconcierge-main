import React from 'react';
import { render, screen } from '@testing-library/react';
import DateCalendarInput from './DateCalendarInput';

jest.mock('@inertiajs/react', () => ({
	usePage: () => ({ props: { user: {} } }),
}));

jest.mock('@headlessui/react', () => {
	const React = require('react');
	const Wrapper = ({ as: Component = 'div', children, ...props }) => <Component {...props}>{children}</Component>;

	return {
		Popover: Object.assign(Wrapper, { Button: Wrapper, Panel: Wrapper }),
		Transition: ({ children }) => <>{children}</>,
	};
});

jest.mock('react-day-picker', () => ({
	DayPicker: ({ mode, selected }) => (
		<div data-testid="day-picker" data-mode={mode} data-selected={selected?.toISOString()} />
	),
}));

jest.mock('react-day-picker/style.css', () => ({}), { virtual: true });

describe('DateCalendarInput', () => {
	it('passes the date-only value as the selected local calendar day in single mode', () => {
		const expectedDate = new Date(2025, 0, 15);

		render(<DateCalendarInput name="date" value="2025-01-15" updateFn={jest.fn()} />);

		const dayPicker = screen.getByTestId('day-picker');
		expect(dayPicker.getAttribute('data-mode')).toBe('single');
		expect(dayPicker.getAttribute('data-selected')).toBe(expectedDate.toISOString());
	});
});