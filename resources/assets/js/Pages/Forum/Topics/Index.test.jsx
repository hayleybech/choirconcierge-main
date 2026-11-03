import React from 'react';
import { render, screen, within } from '@testing-library/react';
import Index from './Index';

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({
		route: (name, params) =>
			`/${name}${params?.singer ? `/${params.singer}` : ''}${params?.filter ? `?filter=${params.filter}` : ''}`,
	}),
}));
vi.mock('../../../components/AppHead', () => ({ default: () => null }));
vi.mock('../ForumPageLayout', () => ({ default: ({ children }) => children }));
vi.mock('./TopicList', () => ({ default: () => null }));

describe('Forum topics index', () => {
	test('shows the latest pinned thread with author, channel, and a short content preview', () => {
		render(
			<Index
				category={null}
				topicCategories={[{ id: 1, title: 'Announcements', topics_count: 1 }]}
				topics={[
					{
						id: 1,
						title: 'Welcome to the forum',
						created_at: '2026-10-28T12:00:00Z',
						author_name: 'Alex Singer',
						author_membership_id: 10,
						author_avatar_url: '/alex.jpg',
						body_html: '<p>Here are the first few lines of this thread.</p>',
						category_id: 1,
						category_title: 'Announcements',
						is_pinned: true,
					},
				]}
				canCreateTopic={false}
			/>
		);

		const latestPinnedThread = screen.getByRole('region', { name: 'Latest pinned thread' });
		const author = screen.getByText('Alex Singer');
		const channel = within(latestPinnedThread).getByText('Announcements');
		const channelLink = channel.closest('a');
		const threadTitle = within(latestPinnedThread).getByRole('link', { name: 'Welcome to the forum' });
		const bodyPreview = screen.getByText('Here are the first few lines of this thread.');

		expect(latestPinnedThread.querySelector('img[src="/alex.jpg"]')).toBeTruthy();
		expect(author.closest('a')?.getAttribute('href')).toBe('/singers.show/10');
		expect(threadTitle.className).toContain('text-purple-700');
		expect(threadTitle.className).toContain('font-normal');
		expect(author.parentElement).toBe(channelLink.parentElement);
		expect(channelLink.previousElementSibling.getAttribute('aria-hidden')).toBe('true');
		expect(channelLink.previousElementSibling.querySelector('.fa-chevron-right')).toBeTruthy();
		expect(bodyPreview.parentElement.className).toContain('line-clamp-3');
	});

	test('shows channel counts without dividers and offers the unread filter', () => {
		render(
			<Index
				category={null}
				topicCategories={[
					{ id: 1, title: 'Announcements', topics_count: 4 },
					{ id: 2, title: 'Rehearsals', topics_count: 1 },
				]}
				topics={[]}
				canCreateTopic={false}
				filter="unread"
			/>
		);

		const channelsCard = screen.getByText('Channels').closest('section');
		const channelList = channelsCard.querySelector('ul');

		expect(screen.getByText('4 threads')).toBeTruthy();
		expect(screen.getByText('1 thread')).toBeTruthy();
		expect(channelList.className).not.toContain('divide-y');
		expect(screen.getByRole('navigation', { name: 'Thread filters' })).toBeTruthy();
		expect(screen.getByRole('link', { name: 'Unread' }).getAttribute('aria-current')).toBe('page');
		expect(screen.getByRole('link', { name: 'Unread' }).getAttribute('href')).toBe(
			'/forum.topics.index?filter=unread'
		);
	});
});
