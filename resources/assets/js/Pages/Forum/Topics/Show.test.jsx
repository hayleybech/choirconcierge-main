import React from 'react';
import { render, screen, within } from '@testing-library/react';
import Show from './Show';

vi.mock('@inertiajs/react', () => ({
	Link: ({ children, ...props }) => <a {...props}>{children}</a>,
	router: { delete: vi.fn(), post: vi.fn(), put: vi.fn() },
	useForm: () => ({
		data: { title: '', body: '' },
		setData: vi.fn(),
		put: vi.fn(),
		post: vi.fn(),
		processing: false,
		errors: {},
	}),
}));

vi.mock('../../../Layouts/TenantLayout', () => ({ default: ({ children }) => <div>{children}</div> }));
vi.mock('../../../components/AppHead', () => ({ default: () => null }));
vi.mock('../../../components/Badge', () => ({ default: ({ children }) => <span>{children}</span> }));
vi.mock('../../../components/Icon', () => ({
	default: ({ icon, className }) => <i className={`fa-${icon} ${className ?? ''}`} />,
}));
vi.mock('../../../components/Prose', () => ({
	default: ({ content, className }) => <div className={className} dangerouslySetInnerHTML={{ __html: content }} />,
}));
vi.mock('../../../components/inputs/ButtonV2', () => ({
	default: ({ children, intent, weight, ...props }) => (
		<button data-intent={intent} data-weight={weight} {...props}>
			{children}
		</button>
	),
}));
vi.mock('../../../components/PageTopBar', () => ({
	__esModule: true,
	default: ({ children }) => <div>{children}</div>,
	PageActionsMenu: ({ children }) => <div>{children}</div>,
	PageTopNavigation: ({ children }) => <div>{children}</div>,
}));
vi.mock('../../../components/ActionMenu/ActionMenuItem', () => ({
	default: ({ children, ...props }) => <button {...props}>{children}</button>,
}));
vi.mock('../../../components/PageHeader/PageHeader', () => ({
	PageHeader: ({ children }) => <header data-testid="thread-page-header">{children}</header>,
	PageHeaderActions: ({ children }) => <div data-testid="thread-header-actions">{children}</div>,
	PageHeaderBreadcrumbs: () => null,
	PageHeaderContent: ({ children }) => <div data-testid="thread-header-content">{children}</div>,
	PageHeaderMeta: ({ children }) => <div data-testid="thread-metadata">{children}</div>,
	PageHeaderTitle: ({ children }) => <h2>{children}</h2>,
}));
vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: (name, params) => `/${name}${params?.singer ? `/${params.singer}` : ''}` }),
}));
vi.mock('../ChannelBadge', () => ({ default: ({ name }) => <span>#{name}</span> }));
vi.mock('./ReactionSummary', () => ({ default: () => <div data-testid="reaction-summary" /> }));

const createProps = overrides => ({
	category: { id: 8, title: 'Announcements' },
	topic: {
		id: 5,
		title: 'Pinned thread',
		body: 'Thread body copy',
		body_html: '<p>Thread body copy</p>',
		created_at: '2026-10-28T12:00:00Z',
		author_name: 'Thread author',
		author_membership_id: 10,
		author_avatar_url: '/author.jpg',
		is_pinned: true,
		is_locked: false,
		is_deleted: false,
		is_following: false,
		can_edit: true,
		reactions: [],
	},
	replies: [
		{
			id: 21,
			parent_id: null,
			author_name: 'Reply author',
			author_membership_id: 12,
			created_at: '2026-10-28T12:00:00Z',
			body_html: '<p>Reply body copy</p>',
			is_deleted: false,
			can_edit: true,
			can_restore: true,
			reactions: [],
		},
	],
	canModerate: true,
	canReply: true,
	setSidebarOpen: vi.fn(),
	...overrides,
});

describe('Forum thread show page', () => {
	it('renders follow controls as an icon-only button at the top of the page header', () => {
		const props = createProps();
		const { rerender } = render(<Show {...props} />);
		const pageHeader = screen.getByTestId('thread-page-header');
		const headerActions = screen.getByTestId('thread-header-actions');
		const followButton = screen.getByRole('button', { name: 'Follow thread' });
		const actionPosition = headerActions.parentElement;
		const actionColumn = followButton.parentElement;

		expect(pageHeader.contains(followButton)).toBe(true);
		expect(headerActions.contains(followButton)).toBe(false);
		expect(actionPosition.className).toContain('xl:mt-auto');
		expect(actionColumn.className).toContain('xl:flex-col');
		expect(actionColumn.className).toContain('xl:self-stretch');
		expect(followButton.textContent).toBe('');
		expect(followButton.querySelector('.fa-bookmark')).toBeTruthy();

		rerender(<Show {...createProps({ topic: { ...props.topic, is_following: true } })} />);

		expect(screen.getByRole('button', { name: 'Unfollow thread' })).toBeTruthy();
	});

	it('renders thread content in its page header and aligns reply reactions with controls', () => {
		render(<Show {...createProps()} />);

		const pageHeader = screen.getByTestId('thread-page-header');
		const title = within(pageHeader).getByRole('heading', { name: 'Pinned thread' });
		const metadata = screen.getByTestId('thread-metadata');
		const body = screen.getByText('Thread body copy');
		const reactionSummary = screen.getAllByTestId('reaction-summary')[1];
		const replyFooter = reactionSummary.parentElement;

		expect(title.firstElementChild.firstElementChild.classList.contains('fa-thumbtack')).toBe(true);
		expect(
			within(metadata)
				.getByRole('link', { name: 'Thread author' })
				.getAttribute('href')
		).toBe('/singers.show/10');
		expect(within(metadata).getByText('#Announcements')).toBeTruthy();
		expect(body.parentElement.previousElementSibling).toBe(metadata);
		expect(body.parentElement.className).toContain('mt-4');
		expect(screen.getByRole('link', { name: 'Reply author' }).getAttribute('href')).toBe('/singers.show/12');
		expect(replyFooter.classList.contains('md:flex-row')).toBe(true);
		expect(within(replyFooter).getByRole('button', { name: /reply/i })).toBeTruthy();
		expect(screen.getByRole('main').querySelector('.max-w-5xl').className).not.toContain('mx-auto');
	});

	it('uses secondary intents for edit, pin, and lock and primary intents for unpin and unlock', () => {
		const props = createProps();
		const { rerender } = render(<Show {...props} />);
		const headerActions = screen.getByTestId('thread-header-actions');

		expect(
			within(headerActions)
				.getByRole('button', { name: 'Edit' })
				.getAttribute('data-intent')
		).toBe('secondary');
		expect(
			within(headerActions)
				.getByRole('button', { name: 'Lock' })
				.getAttribute('data-intent')
		).toBe('secondary');
		expect(
			within(headerActions)
				.getByRole('button', { name: 'Unpin' })
				.getAttribute('data-intent')
		).toBe('primary');

		rerender(
			<Show
				{...createProps({
					topic: { ...props.topic, is_locked: true, is_pinned: false },
				})}
			/>
		);

		expect(
			within(headerActions)
				.getByRole('button', { name: 'Unlock' })
				.getAttribute('data-intent')
		).toBe('primary');
		expect(
			within(headerActions)
				.getByRole('button', { name: 'Pin' })
				.getAttribute('data-intent')
		).toBe('secondary');
	});

	it('only shows restore actions for deleted threads and replies', () => {
		const props = createProps();
		const { rerender } = render(<Show {...props} />);

		expect(screen.queryByRole('button', { name: /restore/i })).toBeNull();

		rerender(
			<Show
				{...createProps({
					topic: { ...props.topic, is_deleted: true },
					replies: [{ ...props.replies[0], is_deleted: true }],
				})}
			/>
		);

		const headerActions = screen.getByTestId('thread-header-actions');
		const reply = screen.getByRole('article');
		expect(within(headerActions).getByRole('button', { name: /restore thread/i })).toBeTruthy();
		expect(within(reply).getByRole('button', { name: 'Restore' })).toBeTruthy();
	});
});
