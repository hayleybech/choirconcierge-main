import Icon from './Icon';
import { Link } from '@inertiajs/react';
import classNames from '../classNames';

const Pagination = ({details}) => (
	<nav className="flex items-center justify-between border-t border-gray-200 px-4 pb-4 bg-white">
		<div className="pt-4 text-sm text-gray-500 pr-2 flex-grow">
			{details.total > 0 ? (
				<>Showing <strong>{details.from}</strong> to <strong>{details.to}</strong> of <strong>{details.total}</strong> results</>
			) : 'No results found'}
		</div>

		<div className="flex items-center justify-between">
			<div className="-mt-px flex flex-grow">
				{!!details.prev_page_url && (
					<Link
						href={details.prev_page_url}
						className="inline-flex items-center border-t-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
					>
						<Icon icon="arrow-left" type="light" mr className="text-gray-400" />
						Previous
					</Link>
				)}
			</div>
			<div className="hidden md:-mt-px md:flex">
				{details.links.filter(item => ! item.label.includes('Previous') && ! item.label.includes('Next'))
					.map(link => (
						link.url === null ? (
							<span key={link.label} className="inline-flex items-center border-t-2 px-4 pt-4 text-sm font-medium border-transparent text-gray-500">{link.label}</span>
						) : (
							<a
								href={link.url}
								key={link.label}
								className={classNames(
									'inline-flex items-center border-t-2 px-4 pt-4 text-sm font-medium',
									link.active ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
								)}
							>
								{link.label}
							</a>
						)
				))}
			</div>
			<div className="-mt-px flex flex-1 justify-end">
				{!!details.next_page_url && (
					<Link
						href={details.next_page_url}
						className="inline-flex items-center border-t-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
					>
						Next
						<Icon icon="arrow-right" type="light" mr className="text-gray-400" />
					</Link>
				)}
			</div>
		</div>
	</nav>
);

export default Pagination;