import { mailIconColours, mailIcons, mailLabels } from './MailStatusTag';

describe('MailStatusTag announcement statuses', () => {
	const statuses = {
		'audience-empty': 'No recipients',
		'announcement-skipped': 'Announcement skipped',
		'announcement-sent': 'Announcement sent',
	};

	it.each(Object.entries(statuses))('provides a tag for %s', (status, label) => {
		expect(mailLabels[status]).toBe(label);
		expect(mailIconColours[status]).toBeDefined();
		expect(mailIcons[status]).toBeDefined();
	});
});
