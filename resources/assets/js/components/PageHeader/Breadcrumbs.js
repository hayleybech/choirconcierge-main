import React from 'react';
import { Link } from '@inertiajs/react';
import Icon from '../Icon';

const Breadcrumbs = ({ breadcrumbs, className, showLastChevron = true }) => (
	<nav aria-label="Breadcrumb" className={className}>
		<ol className="flex items-center gap-3">
			{breadcrumbs.map(({ name, url }, index) => (
				<li key={index}>
					<div className="flex items-center gap-3">
						<Link href={url} className="text-sm font-medium text-gray-500 hover:text-gray-700">
							{name}
						</Link>
						{(showLastChevron || index < breadcrumbs.length - 1) && (
							<Icon icon="chevron-right" className="text-gray-400 text-xs" />
						)}
					</div>
				</li>
			))}
		</ol>
	</nav>
);

export default Breadcrumbs;
