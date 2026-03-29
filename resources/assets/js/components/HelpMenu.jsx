import { Menu, Transition } from '@headlessui/react';

import classNames from '../classNames';
import React, { Fragment } from 'react';
import { Link } from '@inertiajs/react';
import Icon from './Icon';
import useRoute from '../hooks/useRoute';

export const HelpMenu = ({ startTour }) => {
	const { route } = useRoute();

	const helpNavigation = [
		{ name: 'Run Tour', action: () => startTour(), icon: 'rocket' },
		{ name: 'Changelog', href: route('central.changelog'), icon: 'code-merge' },
		{
			name: 'Contact Us',
			action: () => window.location.assign('mailto:hayley@choirconcierge.com'),
			icon: 'question',
		},
	];

	return (
		<Menu as="div" className="ml-3 relative">
			<div>
				<Menu.Button className="max-w-xs bg-white flex items-center text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 p-1 text-gray-700 hover:text-gray-600">
					<span className="sr-only">Open help menu</span>
					<Icon icon="question-circle" size="text-xl" />
				</Menu.Button>
			</div>
			<Transition
				as={Fragment}
				enter="transition ease-out duration-100"
				enterFrom="opacity-0 scale-95"
				enterTo="opacity-100 scale-100"
				leave="transition ease-in duration-75"
				leaveFrom="opacity-100 scale-100"
				leaveTo="opacity-0 scale-95"
			>
				<Menu.Items className="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
					{helpNavigation.map(item => (
						<Menu.Item key={item.name}>
							{({ active }) => (
								<>
									{item.href ? (
										<Link
											href={item.href}
											className={classNames(
												active ? 'bg-gray-100' : '',
												'block px-4 py-2 text-sm text-gray-700'
											)}
										>
											<Icon icon={item.icon} mr />
											{item.name}
										</Link>
									) : (
										<button
											onClick={item.action}
											type="button"
											className={classNames(
												active ? 'bg-gray-100' : '',
												'block px-4 py-2 text-sm text-gray-700 w-full text-left'
											)}
										>
											<Icon icon={item.icon} mr />
											{item.name}
										</button>
									)}
								</>
							)}
						</Menu.Item>
					))}
				</Menu.Items>
			</Transition>
		</Menu>
	);
};
