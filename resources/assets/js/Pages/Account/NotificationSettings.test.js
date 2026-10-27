import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import '@testing-library/jest-dom';
import NotificationSettings from './NotificationSettings';

vi.mock('@inertiajs/react', () => ({
	useForm: ({ email_preferences }) => ({
		data: { email_preferences },
		setData: vi.fn(),
		put: vi.fn(),
		processing: false,
		errors: {},
	}),
}));

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: name => name }) }));

const preferences = {
	communications: true,
	songs: true,
	events: true,
	documents: true,
	polls: true,
	attendance_reports: true,
	system: true,
	product: true,
	promotions: false,
	community: false,
};

describe('NotificationSettings attendance reporting preference', () => {
	it('hides the setting from users without attendance report permission', () => {
		render(
			<NotificationSettings
				preferences={preferences}
				accountRoute="account"
				canReceiveAttendanceReports={false}
			/>,
		);

		expect(screen.queryByText('Attendance Reports')).not.toBeInTheDocument();
	});

	it('shows the setting to users with attendance report permission', () => {
		render(
			<NotificationSettings
				preferences={preferences}
				accountRoute="account"
				canReceiveAttendanceReports
			/>,
		);

		expect(screen.getByText('Attendance Reports')).toBeInTheDocument();
	});

	it('shows poll notifications above attendance reporting', () => {
		render(
			<NotificationSettings
				preferences={preferences}
				accountRoute="account"
				canReceiveAttendanceReports
			/>,
		);

		const categoryTitles = screen.getAllByText(/Polls|Attendance Reports/);

		expect(categoryTitles.map(title => title.textContent)).toEqual([
			'Polls',
			'Attendance Reports',
		]);
	});
});
