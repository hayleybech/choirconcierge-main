import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { useMediaQuery } from 'react-responsive';
import SectionLayout from './SectionLayout';

jest.mock('react-responsive', () => ({
	useMediaQuery: jest.fn(),
}));

describe('SectionLayout', () => {
	const sections = [
		{ id: 'personal', column: 0, title: 'Personal Details', content: <div>Personal content</div> },
		{ id: 'membership', column: 0, title: 'Membership Details', content: <div>Membership content</div> },
		{ id: 'attendance', column: 1, title: 'Attendance', content: <div>Attendance content</div> },
		{
			id: 'desktop-only',
			column: 1,
			title: 'Desktop only',
			showOnMobile: false,
			content: <div>Desktop content</div>,
		},
		{ id: 'hidden', column: 1, title: 'Hidden', show: false, content: <div>Hidden content</div> },
	];
	const layout = {
		className: 'grid-cols-1 divide-y divide-gray-300 sm:grid sm:grid-cols-3 sm:divide-x sm:divide-y-0 xl:grid-cols-4',
		columns: [{ className: 'sm:col-span-2' }, { className: 'sm:col-span-1' }],
	};

	it('renders only the desktop layout on desktop', () => {
		useMediaQuery.mockReturnValue(true);

		render(<SectionLayout sections={sections} layout={layout} />);

		expect(screen.queryAllByRole('tab')).toHaveLength(0);
		expect(screen.getAllByRole('heading')).toHaveLength(4);
		expect(screen.getByRole('heading', { name: 'Personal Details' })).toBeTruthy();
		expect(screen.getByRole('heading', { name: 'Desktop only' })).toBeTruthy();
		expect(screen.getByText('Desktop content')).toBeTruthy();
		expect(screen.queryByText('Hidden')).toBeNull();
	});

	it('renders only the mobile layout on mobile', () => {
		useMediaQuery.mockReturnValue(false);

		render(<SectionLayout sections={sections} layout={layout} />);

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
