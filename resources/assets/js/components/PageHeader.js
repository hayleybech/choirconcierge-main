import React from 'react';
import { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import Breadcrumbs from './Breadcrumbs';
import classNames from '../classNames';
import buttonStyles from './inputs/buttonStyles';
import Button from './inputs/Button';
import Icon from './Icon';
import { Link } from '@inertiajs/react';
import ActionMenuItem from './ActionMenu/ActionMenuItem';

const PageHeader = ({ title, image, icon, meta, breadcrumbs, actions = [], optionsVariant }) => {
	const filteredActions = actions.filter(action => !!action);

	return (
		<div className="py-6 bg-white border-b border-gray-300">
			<div className=" px-4 sm:px-6 md:px-8">
				<div className="lg:flex lg:items-center lg:justify-between">
					{image && <img src={image} alt={title} className="h-32 rounded-md mb-3 lg:mb-0 mr-6" />}
					<div className="flex-1 min-w-0">
						<Breadcrumbs breadcrumbs={breadcrumbs} />
						<h2 className="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
							{icon && <Icon icon={icon} type="solid" className="mr-2" />}
							<span>{title}</span>
						</h2>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							{meta}
						</div>
					</div>
					<div className="mt-5 flex sm:flex-row-reverse lg:mt-0 lg:ml-4">
						{filteredActions.map((action, key) => (
							<span className={key === 0 ? 'sm:ml-3' : 'hidden sm:block ml-3'} key={key}>
								{action.label ? (
									<Button
										href={action.url}
										onClick={action.onClick}
										size="sm"
										variant={action.variant}
										external={action.download}
										download={action.download}
									>
										<Icon icon={action.icon} mr />
										{action.label}
									</Button>
								) : (
									action
								)}
							</span>
						))}

						{/* Dropdown */}
						{filteredActions.length > 2 ? (
							<Menu as="span" className="ml-3 relative sm:hidden z-20">
								<Menu.Button className={buttonStyles(optionsVariant, 'sm')}>
									Options
									<Icon icon="chevron-down" type="light" ml className="-mr-1 text-sm" />
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
									<Menu.Items className="origin-top-right absolute right-0 mt-2 -mr-1 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
										{filteredActions.map(
											(action, key) =>
												key > 0 && (
													<ActionMenuItem
														key={key}
														url={action.url}
														onClick={action.onClick}
														download={action.download}
														variant={action.variant}
													>
														<Icon icon={action.icon} mr />
														{action.label}
													</ActionMenuItem>
												)
										)}
									</Menu.Items>
								</Transition>
							</Menu>
						) : (
							filteredActions.length === 2 && (
								<Button
									href={filteredActions[1].url}
									onClick={filteredActions[1].onClick}
									variant={filteredActions[1].variant}
									size="sm"
									className="ml-3 sm:hidden"
								>
									<Icon icon={filteredActions[1].icon} mr />
									{filteredActions[1].label}
								</Button>
							)
						)}
					</div>
				</div>
			</div>
		</div>
	);
};

export default PageHeader;
