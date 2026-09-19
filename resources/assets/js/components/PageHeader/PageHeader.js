import React from 'react';
import Breadcrumbs from './Breadcrumbs';

export const PageHeader = ({ children }) => (
	<div className="pt-4 xl:pt-6 pb-6 bg-white border-b border-gray-300 px-4 sm:px-6 md:px-8">
		<div className="lg:flex lg:items-center lg:justify-between">
			{children}
		</div>
	</div>
);

/** Wrapper for page header desktop actions */
export const PageHeaderActions = ({ children }) => <div className="hidden xl:flex mt-0 lg:ml-4 gap-3">{children}</div>;

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

export const PageHeaderContent = ({children}) => <div className="flex-1 min-w-0 space-y-2">{children}</div>;

export const PageHeaderTitle = ({ children }) => (
	<h2 className="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">{children}</h2>
);
