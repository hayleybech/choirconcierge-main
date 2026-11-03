import React from 'react';
import { Link } from '@inertiajs/react';

const TopicFilter = ({ filter = 'all', allHref, unreadHref }) => (
	<nav className="flex items-center gap-2" aria-label="Thread filters">
		<FilterLink href={allHref} active={filter === 'all'}>
			All threads
		</FilterLink>
		<FilterLink href={unreadHref} active={filter === 'unread'}>
			Unread
		</FilterLink>
	</nav>
);

const FilterLink = ({ href, active, children }) => (
	<Link
		href={href}
		aria-current={active ? 'page' : undefined}
		className={`rounded-full border px-3 py-1.5 text-sm font-medium transition-colors ${
			active
				? 'border-purple-600 bg-purple-50 text-purple-800'
				: 'border-gray-300 bg-white text-gray-600 hover:border-purple-300 hover:text-purple-700'
		}`}
	>
		{children}
	</Link>
);

export default TopicFilter;
