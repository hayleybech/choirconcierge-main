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

it('renders related page links after the title', () => {
	render(
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

	expect(title.compareDocumentPosition(link) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
});

it('keeps page actions in the title row and places primary actions last', () => {
	const HeaderAction = ({ intent, children }) => <button data-intent={intent}>{children}</button>;
	render(
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
	expect(Array.from(actions.querySelectorAll('button')).map(button => button.textContent)).toEqual([
		'Add New',
		'Export',
	]);
});
