import React from 'react';
import { render, screen } from '@testing-library/react';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderMeta,
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

it('keeps page actions in the title row and places primary actions last', () => {
	const HeaderAction = ({ intent, children }) => <button data-intent={intent}>{children}</button>;
	const { container } = render(
		<PageHeader>
			<PageHeaderContent>
				<PageHeaderTitleRow>
					<PageHeaderTitle>Events</PageHeaderTitle>
					<PageHeaderLinks>
						<PageHeaderLink href="/event-types">Event Types</PageHeaderLink>
					</PageHeaderLinks>
					<PageHeaderActions>
						<HeaderAction intent="primary">Add New</HeaderAction>
						<HeaderAction intent="secondary">Export</HeaderAction>
					</PageHeaderActions>
				</PageHeaderTitleRow>
				<PageHeaderMeta>Supporting page details</PageHeaderMeta>
			</PageHeaderContent>
		</PageHeader>
	);
	const title = screen.getByRole('heading', { name: 'Events' });
	const addNew = screen.getByRole('button', { name: 'Add New' });
	const actions = addNew.parentElement;

	expect(actions.parentElement).toBe(title.parentElement);
	expect(actions.className).toContain('xl:ml-auto');
	expect(Array.from(actions.querySelectorAll('button')).map(button => button.textContent)).toEqual([
		'Export',
		'Add New',
	]);
});
