import React from 'react';
import { render, screen } from '@testing-library/react';
import {
	PageHeader,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from './PageHeader/PageHeader';

it('can hide index page headers on mobile', () => {
	const { container } = render(
		<PageHeader hideOnMobile>
			<PageHeaderTitle>Index title</PageHeaderTitle>
		</PageHeader>
	);

	expect(container.firstElementChild.className).toContain('hidden');
	expect(container.firstElementChild.className).toContain('xl:block');
	expect(screen.getByRole('heading', { name: 'Index title' })).toBeTruthy();
});

it('does not offset page titles for baseline alignment', () => {
	const { container } = render(<PageHeaderTitle>Page title</PageHeaderTitle>);

	expect(container.firstElementChild.className).not.toContain('-mt-2');
});

it('centers related page links beside the title', () => {
	const { container } = render(
		<PageHeaderTitleRow>
			<PageHeaderTitle>Events</PageHeaderTitle>
			<PageHeaderLinks>
				<PageHeaderLink href="/event-types" icon="tags">
					Event Types
				</PageHeaderLink>
			</PageHeaderLinks>
		</PageHeaderTitleRow>
	);
	const title = screen.getByRole('heading', { name: 'Events' });
	const link = screen.getByRole('link', { name: 'Event Types' });

	expect(container.firstElementChild.className).toContain('items-center');
	expect(title.compareDocumentPosition(link) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
});
