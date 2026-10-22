import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import NotificationSettings from './NotificationSettings';

jest.mock('@inertiajs/react', () => ({
	useForm: ({ email_preferences }) => ({
		data: { email_preferences },
		setData: jest.fn(),
		put: jest.fn(),
		processing: false,
		errors: {},
	}),
}));

jest.mock('../../hooks/useRoute', () => () => ({ route: name => name }));

const preferences = {
	communications: true,
	songs: true,
	events: true,
	documents: true,
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

		expect(screen.queryByText('Attendance Reporting')).not.toBeInTheDocument();
	});

	it('shows the setting to users with attendance report permission', () => {
		render(
			<NotificationSettings
				preferences={preferences}
				accountRoute="account"
				canReceiveAttendanceReports
			/>,
		);

		expect(screen.getByText('Attendance Reporting')).toBeInTheDocument();
	});
});
