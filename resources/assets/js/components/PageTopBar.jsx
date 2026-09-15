import Icon from './Icon';
import { Link } from '@inertiajs/react';
import { Menu, Transition } from '@headlessui/react';
import React, { Fragment } from 'react';
import Breadcrumbs from './PageHeader/Breadcrumbs';
import { useMediaQuery } from 'react-responsive';
import { useSidebar } from '../contexts/sidebar-context';

const PageTopBar = ({ children }) => {
	const { setSidebarOpen } = useSidebar()

	return (
		<div className="fixed left-0 right-0 z-10 flex h-12 shrink-0 items-center justify-between border-b border-gray-300 bg-white xl:hidden">
			<DrawerOpenButton setOpen={setSidebarOpen} />

			{children}
		</div>
	);
};

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

export const PageActionsMenu = ({ children }) => {
	if(!children?.length > 0) {
		return null;
	}
	return (
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
}

/**
 * Use for Tier 2+ pages
 * Usually paired with PageTopBarTitle
 */
export const PageTopNavigation = ({ breadcrumbs, title, children }) => {
	const isMobile = useMediaQuery({
		query: '(max-width: 639px)',
	});

	return (
		<div className="flex h-full min-w-0 grow">
			{breadcrumbs?.length > 1 && isMobile && <BackButton url={breadcrumbs[0].url} className="sm:hidden" />}
			<div className="flex min-w-0 grow items-center pl-3">
				{breadcrumbs?.length > 1 && !isMobile && (
					<Breadcrumbs breadcrumbs={breadcrumbs?.slice(0, -1)} className="mr-3" />
				)}
				<PageTopBarTitle>{breadcrumbs?.[breadcrumbs.length - 1]?.name ?? title}</PageTopBarTitle>

				<div className="flex shrink-0 items-center justify-end gap-3 pl-2">{children}</div>
			</div>
		</div>
	);
};

/** Use by itself for Tier 1 pages */
export const PageTopBarTitle = ({ children }) => (
	<h1 className="min-w-0 flex-1 truncate text-base font-semibold text-gray-900">{children}</h1>
);

export const PageTopBarContent = ({ children }) => (
	<div className="flex items-center grow justify-between pl-2 gap-3">{children}</div>
);
