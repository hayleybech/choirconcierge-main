import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useMediaQuery } from 'react-responsive';
import SectionLayout from './SectionLayout';

jest.mock('react-responsive', () => ({
	useMediaQuery: jest.fn(),
}));

describe('SingerSectionLayout', () => {
	const columns = [
		[
			{ title: 'Personal Details', content: <div>Personal content</div> },
			{ title: 'Membership Details', content: <div>Membership content</div> },
		],
		[
			{ title: 'Attendance', content: <div>Attendance content</div> },
			{
				title: 'Desktop only',
				showOnMobile: false,
				collapsible: false,
				content: <div>Desktop content</div>,
			},
			{ title: 'Hidden', show: false, content: <div>Hidden content</div> },
		],
	];

	it('renders only the desktop layout on desktop', () => {
		useMediaQuery.mockReturnValue(true);

		render(
			<SectionLayout columns={columns} />
		);

		expect(screen.queryAllByRole('tab')).toHaveLength(0);
		expect(screen.getAllByRole('heading')).toHaveLength(4);
		expect(screen.getByRole('heading', { name: 'Personal Details' })).toBeTruthy();
		expect(screen.getByRole('heading', { name: 'Desktop only' })).toBeTruthy();
		expect(screen.getByText('Desktop content')).toBeTruthy();
		expect(screen.queryByText('Hidden')).toBeNull();
	});

	it('renders only the mobile layout on mobile', () => {
		useMediaQuery.mockReturnValue(false);

		render(<SectionLayout columns={columns} />);

		const tabs = screen.getAllByRole('tab');
		expect(tabs).toHaveLength(3);
		expect(screen.getByRole('tabpanel').textContent).toContain('Personal content');
		fireEvent.click(tabs[1]);
		expect(screen.getByRole('tabpanel').textContent).toContain('Membership content');
		fireEvent.click(tabs[2]);
		expect(screen.getByRole('tabpanel').textContent).toContain('Attendance content');
		expect(screen.queryByText('Hidden')).toBeNull();
		expect(screen.queryByText('Desktop only')).toBeNull();
		expect(screen.queryByText('Desktop content')).toBeNull();
	});
});
