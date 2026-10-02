import { Menu, Transition } from '@headlessui/react';
import buttonStyles from '../inputs/buttonStyles';
import Icon from '../Icon';
import React, { Fragment } from 'react';
import classNames from '../../classNames';
import { Link } from '@inertiajs/react';
import LearningStatus from '../../LearningStatus';

const LearningStatusDropdown = ({
	status,
	allowedStatuses = Object.keys(LearningStatus.statuses),
	href,
	method = 'put',
	size = 'sm',
	compact = false,
}) => {
	const current = new LearningStatus(status);

	return (
		allowedStatuses.length > 0 && (
			<Menu
				as="div"
				className={classNames('relative inline-block overflow-visible', compact ? 'grow' : 'w-full sm:w-auto')}
			>
				<Menu.Button
					className={buttonStyles(
						'secondary',
						compact ? 'xs' : size,
						false,
						compact ? 'relative z-0 w-full md:w-auto' : 'relative z-0 w-full md:w-auto'
					)}
				>
					<Icon icon={current.icon} className={current.textColour} />
					<span className={current.textColour}>{compact ? current.shortTitle : current.title}</span>
					<Icon icon="chevron-down" className="text-gray-500" />
				</Menu.Button>

				<Transition
					as={Fragment}
					enter="transition ease-out duration-200"
					enterFrom="opacity-0 scale-95"
					enterTo="opacity-100 scale-100"
					leave="transition ease-in duration-75"
					leaveFrom="opacity-100 scale-100"
					leaveTo="opacity-0 scale-95"
				>
					<Menu.Items
						className={classNames(
							'origin-top-right absolute z-10 left-0 mt-2 -mr-1 rounded-md shadow-lg py-1 bg-white ring-1 ring-black/5 focus:outline-none',
							compact ? 'w-40' : 'min-w-full md:min-w-0 md:w-48'
						)}
					>
						{allowedStatuses
							.filter(slug => slug !== status)
							.map(slug => {
								const option = new LearningStatus(slug);

								return (
									<Menu.Item key={slug}>
										{({ active }) => (
											<Link
												href={href}
												preserveScroll
												method={method}
												data={{ status: slug }}
												className={classNames(
													'block w-full text-left px-4 py-2 text-sm',
													option.textColour,
													active ? 'bg-gray-100' : ''
												)}
											>
												<Icon icon={option.icon} mr className={option.textColour} />
												{compact ? option.shortTitle : option.title}
											</Link>
										)}
									</Menu.Item>
								);
							})}
					</Menu.Items>
				</Transition>
			</Menu>
		)
	);
};

export default LearningStatusDropdown;
