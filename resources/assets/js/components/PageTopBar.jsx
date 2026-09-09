import Icon from './Icon';
import { Link } from '@inertiajs/react';
import { Menu, Transition } from '@headlessui/react';
import React, { Fragment } from 'react';
import Breadcrumbs from './PageHeader/Breadcrumbs';

const PageTopBar = ({ setSidebarOpen, children }) => (
	<div className="relative z-10 flex h-12 shrink-0 items-center justify-between border-b border-gray-300 bg-white lg:hidden">
		<DrawerOpenButton setOpen={setSidebarOpen} />

		{children}
	</div>
);

export default PageTopBar;

export const DrawerOpenButton = ({ setOpen }) => (
	<button
		type="button"
		className="border-r border-gray-200 text-gray-500 px-4 h-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 xl:hidden"
		onClick={() => setOpen(true)}
	>
		<span className="sr-only">Open sidebar</span>
		<Icon icon="bars" />
	</button>
);

export const BackButton = ({ url, className }) => (
	<Link
		href={url}
		className={
			'flex h-full shrink-0 items-center px-4 border-r border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 ' +
			className
		}
	>
		<span className="sr-only">Back to parent page</span>
		<Icon icon="chevron-left" />
	</Link>
);

export const PageActionsMenu = ({ children }) => (
	<Menu as="div" className="relative ml-3 mr-2 shrink-0">
		<Menu.Button className="flex h-10 w-10 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
			<span className="sr-only">Open actions</span>
			<Icon icon="ellipsis-v" />
		</Menu.Button>
		<Transition
			as={Fragment}
			enter="transition ease-out duration-100"
			enterFrom="opacity-0 scale-95"
			enterTo="opacity-100 scale-100"
			leave="transition ease-in duration-75"
			leaveFrom="opacity-100 scale-100"
			leaveTo="opacity-0 scale-95"
		>
			<Menu.Items className="absolute right-0 z-20 mt-2 w-52 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
				{children}
			</Menu.Items>
		</Transition>
	</Menu>
);

/**
 * Use for Tier 2+ pages
 * Usually paired with PageTopBarTitle
 */
export const PageTopNavigation = ({ breadcrumbs, backUrl, children }) => (
	<div className="flex">
		<BackButton url={backUrl} className="sm:hidden" />
		<div className="flex items-center ml-4 gap-3">
			<Breadcrumbs breadcrumbs={breadcrumbs} />
			{children}
		</div>
	</div>
);

/** Use by itself for Tier 1 pages */
export const PageTopBarTitle = ({ title }) => (
	<h1 className="truncate text-base font-semibold text-gray-900">{title}</h1>
);
