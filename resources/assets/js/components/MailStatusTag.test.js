import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import MailStatusTag, { mailIcons, mailLabels } from './MailStatusTag';

describe('MailStatusTag announcement statuses', () => {
	const statuses = {
		'audience-empty': 'No recipients',
		'announcement-skipped': 'Announcement skipped',
		'announcement-sent': 'Announcement sent',
	};

	it.each(Object.entries(statuses))('provides a tag for %s', (status, label) => {
		expect(mailLabels[status]).toBe(label);
		expect(mailIcons[status]).toBeDefined();
	});

	it('renders an announcement status label', () => {
		render(React.createElement(MailStatusTag, { event: { status: 'announcement-sent' } }));

		expect(screen.getByText('Announcement sent')).toBeInTheDocument();
	});
});
