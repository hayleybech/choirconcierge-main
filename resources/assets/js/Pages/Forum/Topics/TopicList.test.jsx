import React from 'react';
import { render, screen } from '@testing-library/react';
import TopicList from './TopicList';

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: (name, params) => `/${name}${params?.singer ? `/${params.singer}` : ''}` }),
}));

describe('TopicList', () => {
	beforeEach(() => {
		vi.useFakeTimers();
		vi.setSystemTime(new Date('2026-10-31T13:30:00Z'));
	});

	afterEach(() => {
		vi.useRealTimers();
	});

	test('shows the topic author avatar next to their name in the table layout', () => {
		render(
			<TopicList
				showTable
				topics={[
					{
						id: 1,
						title: 'Rehearsal plans',
						created_at: '2026-10-28T12:00:00Z',
						author_name: 'Alex Singer',
						author_membership_id: 10,
						author_avatar_url: '/alex.jpg',
						replies_count: 0,
						reactions: [],
					},
				]}
			/>
		);

		const authorName = screen.getByText(/Alex Singer/);

		expect(
			authorName
				.closest('div')
				.querySelector('img')
				?.getAttribute('src')
		).toBe('/alex.jpg');
		expect(authorName.closest('a')?.getAttribute('href')).toBe('/singers.show/10');
	});

	test('shows thread details and reactions in the thread column', () => {
		render(
			<TopicList
				showTable
				channels={[
					{ id: 1, title: 'Rehearsal & Performance' },
					{ id: 2, title: 'Music & Repertoire' },
				]}
				topics={[
					{
						id: 1,
						title: 'Rehearsal plans',
						created_at: '2026-10-28T12:00:00Z',
						author_name: 'Alex Singer',
						author_membership_id: 10,
						author_avatar_url: '/alex.jpg',
						body_html: '<p>One line body preview</p>',
						category_id: 2,
						category_title: 'Music & Repertoire',
						is_pinned: true,
						is_unread: true,
						replies_count: 1,
						latest_reply: {
							author_name: 'Jamie Singer',
							author_membership_id: 12,
							author_avatar_url: '/jamie.jpg',
							created_at: '2026-10-29T13:30:00Z',
						},
						reactions: [{ emoji: '👍', count: 2, reactors: [] }],
					},
				]}
			/>
		);

		const author = screen.getByText('Alex Singer');
		const activityAuthor = screen.getByText('Jamie Singer');
		const threadTitle = screen.getByRole('link', { name: 'Rehearsal plans' });
		const createdAt = screen.getByText('3 days ago');
		const latestReplyAt = screen.getByText('2 days ago');
		const bodyPreview = screen.getByText('One line body preview');
		const reaction = screen.getByLabelText('👍 reaction, 2');
		const reactions = screen.getByLabelText('Reactions');
		const pinnedIcon = screen.getByRole('img', { name: 'Pinned thread' });
		const channelName = screen.getByText('Music & Repertoire');
		const channelLink = channelName.closest('a');

		expect(screen.getByRole('columnheader', { name: 'Thread' })).toBeTruthy();
		expect(screen.queryByRole('columnheader', { name: 'Channel' })).toBeNull();
		expect(screen.getByRole('columnheader', { name: 'Created' })).toBeTruthy();
		expect(screen.getByRole('columnheader', { name: 'Activity' })).toBeTruthy();
		expect(screen.queryByRole('columnheader', { name: 'Reactions' })).toBeNull();
		expect(author.compareDocumentPosition(threadTitle) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
		expect(author.closest('a')?.getAttribute('href')).toBe('/singers.show/10');
		expect(activityAuthor.closest('a')?.getAttribute('href')).toBe('/singers.show/12');
		expect(threadTitle.className).toContain('font-semibold');
		expect(threadTitle.className).toContain('text-purple-700');
		expect(pinnedIcon.compareDocumentPosition(threadTitle) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
		expect(pinnedIcon.className).toContain('text-lg');
		expect(pinnedIcon.parentElement.parentElement.className).toContain('items-center');
		expect(threadTitle.closest('td').contains(bodyPreview)).toBe(true);
		expect(bodyPreview.parentElement.className).toContain('line-clamp-1');
		expect(threadTitle.closest('td').contains(createdAt)).toBe(false);
		expect(createdAt.getAttribute('datetime')).toBe('2026-10-28T12:00:00Z');
		expect(activityAuthor.compareDocumentPosition(latestReplyAt) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
		expect(
			activityAuthor
				.closest('td')
				.querySelector('img')
				?.getAttribute('src')
		).toBe('/jamie.jpg');
		expect(latestReplyAt.getAttribute('datetime')).toBe('2026-10-29T13:30:00Z');
		expect(threadTitle.closest('td').contains(reactions)).toBe(true);
		expect(reaction.className).toContain('text-xs');
		expect(threadTitle.closest('tr').className).not.toContain('bg-purple-50');
		expect(screen.getByRole('img', { name: 'Unread thread' })).toBeTruthy();
		expect(author.closest('td')).toBe(channelName.closest('td'));
		expect(author.compareDocumentPosition(channelName) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
		expect(channelLink.previousElementSibling.getAttribute('aria-hidden')).toBe('true');
		expect(channelLink.previousElementSibling.querySelector('.fa-chevron-right')).toBeTruthy();
		expect(screen.queryByText('>')).toBeNull();
		expect(channelName.previousElementSibling.textContent).toBe('#');
		expect(channelName.parentElement.className).toContain('gap-1');
		expect(channelName.parentElement.parentElement.className).toContain('bg-blue-100');
	});

	test('uses regular purple text for read thread titles', () => {
		render(
			<TopicList
				showTable
				topics={[
					{
						id: 1,
						title: 'Already read',
						created_at: '2026-10-28T12:00:00Z',
						is_unread: false,
						reactions: [],
					},
				]}
			/>
		);

		const threadTitle = screen.getByRole('link', { name: 'Already read' });

		expect(threadTitle.className).toContain('font-normal');
		expect(threadTitle.className).not.toContain('font-semibold');
		expect(threadTitle.className).toContain('text-purple-700');
		expect(threadTitle.closest('tr').className).toContain('bg-gray-100');
	});

	test('uses a chevron icon between the author and channel in the card layout', () => {
		render(
			<TopicList
				topics={[
					{
						id: 1,
						title: 'Rehearsal plans',
						created_at: '2026-10-28T12:00:00Z',
						is_pinned: true,
						is_unread: false,
						author_name: 'Alex Singer',
						author_membership_id: 10,
						category_id: 1,
						category_title: 'Rehearsals',
						reactions: [],
					},
				]}
			/>
		);

		const channelLink = screen.getByText('Rehearsals').closest('a');
		const author = screen.getByText('Alex Singer');
		const threadTitle = screen.getByRole('link', { name: 'Rehearsal plans' });
		const pinnedIcon = screen.getByRole('img', { name: 'Pinned thread' });
		const topicCard = threadTitle.closest('article');

		expect(author.closest('a')?.getAttribute('href')).toBe('/singers.show/10');
		expect(threadTitle.className).toContain('text-purple-700');
		expect(topicCard.className).toContain('bg-gray-100');
		expect(topicCard.className).not.toContain('bg-purple-50');
		expect(pinnedIcon.className).toContain('text-lg');
		expect(pinnedIcon.parentElement.parentElement.className).toContain('items-center');
		expect(channelLink.previousElementSibling.getAttribute('aria-hidden')).toBe('true');
		expect(channelLink.previousElementSibling.querySelector('.fa-chevron-right')).toBeTruthy();
		expect(screen.queryByText('>')).toBeNull();
	});
});
