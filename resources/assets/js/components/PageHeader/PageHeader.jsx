import React from 'react';
import Breadcrumbs from './Breadcrumbs';
import Icon from '../Icon';
import { Link } from '@inertiajs/react';

export const PageHeader = ({ children, className = '', hideOnMobile = false }) => (
	<div
		className={`${
			hideOnMobile ? 'hidden xl:block ' : ''
		}py-3 xl:py-4 bg-white border-b border-gray-100 px-4 sm:px-6 md:px-8`}
	>
		<div className={`lg:flex lg:items-center lg:justify-between ${className}`}>{children}</div>
	</div>
);

/** Wrapper for page header desktop actions */
export const PageHeaderActions = ({ children }) => (
	<div className="hidden shrink-0 items-center gap-3 xl:ml-auto xl:flex flex-row-reverse">{children}</div>
);

/** Wrapper for page header meta items */
export const PageHeaderMeta = ({ children }) => (
	<div className="flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
		{children}
	</div>
);

export const PageHeaderBreadcrumbs = ({ breadcrumbs }) => (
	<div className="hidden xl:block">
		<Breadcrumbs breadcrumbs={breadcrumbs} showLastChevron={false} />
	</div>
);

export const PageHeaderContent = ({ children }) => <div className="min-w-0 flex-1 space-y-2">{children}</div>;

export const PageHeaderTitleRow = ({ children }) => (
	<div className="flex w-full min-w-0 items-center gap-4">{children}</div>
);

export const PageHeaderLinks = ({ children }) => (
	<nav className="hidden shrink-0 items-center gap-3 xl:flex divide-x *:pr-2">{children}</nav>
);

export const PageHeaderLink = ({ href, icon, children, ...props }) => {
	const linkStyle = 'flex items-center gap-1.5 text-sm font-medium text-purple-700 hover:text-sky-500 cursor-pointer';
	return href ? (
		<Link href={href} className={linkStyle}>
			{icon && <Icon icon={icon} />}
			{children}
		</Link>
	) : (
		<button type="button" className={linkStyle} {...props}>
			{icon && <Icon icon={icon} />}
			{children}
		</button>
	);
};

export const PageHeaderTitle = ({ children }) => (
	<h2 className="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">{children}</h2>
);
