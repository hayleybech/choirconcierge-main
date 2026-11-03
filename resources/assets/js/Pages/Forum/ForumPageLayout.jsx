import React from 'react';
import { Link } from '@inertiajs/react';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/ButtonV2';
import Icon from '../../components/Icon';
import useRoute from '../../hooks/useRoute';

const ForumPageLayout = ({
	title,
	description,
	breadcrumbs = [],
	activeTab = 'latest',
	actions = [],
	setSidebarOpen,
	children,
}) => {
	const { route } = useRoute();
	const pageBreadcrumbs = [{ name: 'Discussions', url: route('forum.topics.index') }, ...breadcrumbs];

	return (
		<>
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					title="Discussions"
					breadcrumbs={breadcrumbs.length > 0 ? pageBreadcrumbs : undefined}
				>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								method={action.method}
								intent={action.intent}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>

			<PageHeader>
				<PageHeaderContent>
					{breadcrumbs.length > 0 && <PageHeaderBreadcrumbs breadcrumbs={pageBreadcrumbs} />}
					<PageHeaderTitle>
						<Icon icon="comments" type="solid" className="mr-2 text-purple-700" />
						{title}
					</PageHeaderTitle>
					{description && <p className="text-sm text-gray-500">{description}</p>}
				</PageHeaderContent>
				<PageHeaderActions>
					{actions.map(action => (
						<Button
							key={action.label}
							href={action.url}
							onClick={action.onClick}
							method={action.method}
							intent={action.intent ?? 'primary'}
							weight={action.weight ?? 'solid'}
							disabled={action.disabled}
						>
							<Icon icon={action.icon} />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<div className="border-b border-gray-300 bg-white px-4 sm:px-6 md:px-8">
				<nav className="mx-auto flex max-w-screen-2xl gap-6" aria-label="Discussions sections">
					<ForumSectionLink
						href={route('forum.topics.index')}
						active={activeTab === 'latest'}
						icon="clock"
					>
						Latest threads
					</ForumSectionLink>
					<ForumSectionLink
						href={route('forum.following.index')}
						active={activeTab === 'following'}
						icon="bookmark"
					>
						Followed threads
					</ForumSectionLink>
					<ForumSectionLink
						href={route('forum.categories.index')}
						active={activeTab === 'categories'}
						icon="layer-group"
					>
						Channels
					</ForumSectionLink>
				</nav>
			</div>

			<div className="min-h-0 flex-1 bg-gray-100 px-4 py-6 sm:px-6 md:px-8">
				<div className="mx-auto w-full max-w-screen-2xl">{children}</div>
			</div>
		</>
	);
};

const ForumSectionLink = ({ href, active, icon, children }) => (
	<Link
		href={href}
		aria-current={active ? 'page' : undefined}
		className={`inline-flex items-center gap-2 border-b-2 px-1 py-3 text-sm font-medium transition-colors ${
			active
				? 'border-purple-600 text-purple-700'
				: 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
		}`}
	>
		<Icon icon={icon} />
		{children}
	</Link>
);

export default ForumPageLayout;
