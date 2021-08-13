/*
  This example requires Tailwind CSS v2.0+

  This example requires some changes to your config:

  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
*/
import React, { Fragment, useState } from 'react'
import { Dialog, Menu, Transition } from '@headlessui/react'
import {
    BellIcon,
    CalendarIcon,
    ChartBarIcon,
    FolderIcon,
    HomeIcon,
    InboxIcon,
    MenuAlt2Icon,
    UsersIcon,
    XIcon,
} from '@heroicons/react/outline'
import { SearchIcon } from '@heroicons/react/solid'
import {Link} from "@inertiajs/inertia-react";

const navigation = [
    { name: 'Dashboard', route: 'dash', icon: 'fa-chart-line', items: [] },
    {
        name: 'Singers',
        route: 'singers.index',
        icon: 'fa-users',
        current: true,
        items: [
            { name: 'All Singers', route: 'singers.index', icon: 'fa-list' },
            { name: 'Add New', route: 'singers.create', icon: 'fa-plus-square' },
            { name: 'Voice Parts', route: 'voice-parts.index', icon: 'fa-users-class' },
            { name: 'Roles', route: 'roles.index', icon: 'fa-user-tag' }
        ]
    },
    {
        name: 'Songs',
        route: 'songs.index',
        icon: 'fa-list-music',
        items: [
            { name: 'All Songs', route: 'songs.index', icon: 'fa-list' },
            { name: 'Add New', route: 'songs.create', icon: 'fa-plus-square' },
        ]
    },
    {
        name: 'Events',
        route: 'events.index',
        icon: 'fa-calendar-alt',
        items: [
            { name: 'All Events', route: 'events.index', icon: 'fa-list' },
            { name: 'Add New', route: 'events.create', icon: 'fa-plus-square' },
            { name: 'Attendance Report', route: 'events.reports.attendance', icon: 'fa-analytics' },
        ]
    },
    {
        name: 'Documents',
        route: 'folders.index',
        icon: 'fa-folders',
        items: [
            { name: 'All Folders', route: 'folders.index', icon: 'fa-list' },
            { name: 'Add Folder', route: 'folders.create', icon: 'fa-plus-square' },
        ]
    },
    {
        name: 'Riser Stacks',
        route: 'stacks.index',
        icon: 'fa-people-arrows',
        items: [
            { name: 'All Stacks', route: 'stacks.index', icon: 'fa-list' },
            { name: 'Add New', route: 'stacks.create', icon: 'fa-plus-square' },
        ]
    },
    {
        name: 'Mailing Lists',
        route: 'groups.index',
        icon: 'fa-mail-bulk',
        items: [
            { name: 'All Lists', route: 'groups.index', icon: 'fa-list' },
            { name: 'Add New', route: 'groups.create', icon: 'fa-plus-square' },
        ]
    },
    {
        name: 'Onboarding',
        route: 'tasks.index',
        icon: 'fa-tasks',
        items: [
            { name: 'All Tasks', route: 'tasks.index', icon: 'fa-list' },
            { name: 'Add New', route: 'tasks.create', icon: 'fa-plus-square' },
        ]
    },
];

const userNavigation = [
    { name: 'Your Profile', href: '#' },
    { name: 'Settings', href: '#' },
    { name: 'Sign out', href: '#' },
]

function classNames(...classes) {
    return classes.filter(Boolean).join(' ')
}

export default function Example() {
    const [sidebarOpen, setSidebarOpen] = useState(false)

    return (
        <div className="h-screen flex overflow-hidden bg-gray-100">
            <Transition.Root show={sidebarOpen} as={Fragment}>
                <Dialog as="div" className="fixed inset-0 flex z-40 md:hidden" onClose={setSidebarOpen}>
                    <Transition.Child
                        as={Fragment}
                        enter="transition-opacity ease-linear duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="transition-opacity ease-linear duration-300"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <Dialog.Overlay className="fixed inset-0 bg-gray-600 bg-opacity-75" />
                    </Transition.Child>
                    <Transition.Child
                        as={Fragment}
                        enter="transition ease-in-out duration-300 transform"
                        enterFrom="-translate-x-full"
                        enterTo="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leaveFrom="translate-x-0"
                        leaveTo="-translate-x-full"
                    >
                        <div className="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-brand-purple-dark">
                            <Transition.Child
                                as={Fragment}
                                enter="ease-in-out duration-300"
                                enterFrom="opacity-0"
                                enterTo="opacity-100"
                                leave="ease-in-out duration-300"
                                leaveFrom="opacity-100"
                                leaveTo="opacity-0"
                            >
                                <div className="absolute top-0 right-0 -mr-12 pt-2">
                                    <button
                                        type="button"
                                        className="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                        onClick={() => setSidebarOpen(false)}
                                    >
                                        <span className="sr-only">Close sidebar</span>
                                        <XIcon className="h-6 w-6 text-white" aria-hidden="true" />
                                    </button>
                                </div>
                            </Transition.Child>

                            <Link href={route('dash')} className="flex-shrink-0 flex justify-center items-center px-4 pb-6">
                                <img src="/img/logo.svg" alt="Choir Concierge" className="h-10 w-auto" />
                            </Link>

                            <div className="flex-shrink-0 flex justify-center items-center px-4 bg-gray-50">
                                <Link href={route('dash')} className="flex justify-center py-4 px-6 bg-gray-50">
                                    <img src="/img/choir-logo.png" className="h-12 w-auto" />
                                </Link>
                            </div>

                            <div className="mt-5 flex-1 h-0 overflow-y-auto">
                                <nav className="px-4 space-y-1">
                                    {navigation.map((item) => (
                                        <div>
                                            <a
                                                key={item.name}
                                                href={route(item.route)}
                                                className={classNames(
                                                    item.current ? 'bg-indigo-50 text-brand-purple-dark' : 'text-indigo-200 hover:bg-purple-800 hover:text-purple-100',
                                                    'group flex items-center px-2 py-2 text-lg font-black uppercase rounded-md'
                                                )}
                                            >
                                                <i className={"fas fa-fw mr-4 " + item.icon} aria-hidden="true" />
                                                {item.name}
                                            </a>
                                            {item.current && (
                                                <div className="flex flex-col mt-1 mb-3">
                                                    {item.items.map((child) => (
                                                        <Link href={route(child.route)} className="px-6 py-1.5 rounded-md text-light text-indigo-50 hover:bg-purple-800 hover:text-purple-50">
                                                            <i className={"far fa-fw mr-3 " + child.icon} /> {child.name}
                                                        </Link>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </nav>
                            </div>
                        </div>
                    </Transition.Child>
                    <div className="flex-shrink-0 w-14" aria-hidden="true">
                        {/* Dummy element to force sidebar to shrink to fit close icon */}
                    </div>
                </Dialog>
            </Transition.Root>

            {/* Static sidebar for desktop */}
            <div className="hidden bg-brand-purple-dark md:flex md:flex-shrink-0">
                <div className="flex flex-col w-64">
                    {/* Sidebar component, swap this element with another sidebar if you like */}
                    <div className="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                        <Link href={route('dash')} className="flex pb-6 px-8">
                            <img src="/img/logo.svg" alt="Choir Concierge" className="h-12 w-auto" />
                        </Link>

                        <Link href={route('dash')} className="flex justify-center mb-4 py-4 px-6 bg-gray-50">
                            <img src="/img/choir-logo.png" className="h-12 w-auto" />
                        </Link>
                        <div className="mt-4 flex-1 flex flex-col">
                            <nav className="flex-1 px-4 space-y-1">
                                {navigation.map((item) => (
                                    <div>
                                        <a
                                            key={item.name}
                                            href={route(item.route)}
                                            className={classNames(
                                                item.current ? 'bg-indigo-50 text-brand-purple-dark' : 'text-indigo-200 hover:bg-purple-800 hover:text-purple-100',
                                                'group flex items-center px-2 py-2 text-base uppercase font-black rounded-md'
                                            )}
                                        >
                                            <i className={"fas fa-fw mr-4 " + item.icon} aria-hidden="true" />
                                            {item.name}
                                        </a>
                                        {item.current && (
                                        <div className="flex flex-col mt-1 mb-3">
                                            {item.items.map((child) => (
                                                <Link href={route(child.route)} className="px-6 py-1.5 rounded-md text-base font-light text-indigo-50 hover:bg-purple-800 hover:text-purple-50">
                                                    <i className={"far fa-fw mr-3 text-indigo-200 " + child.icon} /> {child.name}
                                                </Link>
                                            ))}
                                        </div>
                                        )}
                                    </div>
                                ))}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div className="flex flex-col w-0 flex-1 overflow-hidden">
                <div className="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
                    <button
                        type="button"
                        className="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500 md:hidden"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <span className="sr-only">Open sidebar</span>
                        <MenuAlt2Icon className="h-6 w-6" aria-hidden="true" />
                    </button>
                    <div className="flex-1 px-4 flex justify-between">
                        <div className="flex-1 flex">
                            <form className="w-full flex md:ml-0" action="#" method="GET">
                                <label htmlFor="search-field" className="sr-only">
                                    Search
                                </label>
                                <div className="relative w-full text-gray-400 focus-within:text-gray-600">
                                    <div className="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                        <SearchIcon className="h-5 w-5" aria-hidden="true" />
                                    </div>
                                    <input
                                        id="search-field"
                                        className="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm"
                                        placeholder="Search"
                                        type="search"
                                        name="search"
                                    />
                                </div>
                            </form>
                        </div>
                        <div className="ml-4 flex items-center md:ml-6">
                            <button className="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <span className="sr-only">View notifications</span>
                                <BellIcon className="h-6 w-6" aria-hidden="true" />
                            </button>

                            {/* Profile dropdown */}
                            <Menu as="div" className="ml-3 relative">
                                <div>
                                    <Menu.Button className="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <span className="sr-only">Open user menu</span>
                                        <img
                                            className="h-8 w-8 rounded-full"
                                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                            alt=""
                                        />
                                    </Menu.Button>
                                </div>
                                <Transition
                                    as={Fragment}
                                    enter="transition ease-out duration-100"
                                    enterFrom="transform opacity-0 scale-95"
                                    enterTo="transform opacity-100 scale-100"
                                    leave="transition ease-in duration-75"
                                    leaveFrom="transform opacity-100 scale-100"
                                    leaveTo="transform opacity-0 scale-95"
                                >
                                    <Menu.Items className="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        {userNavigation.map((item) => (
                                            <Menu.Item key={item.name}>
                                                {({ active }) => (
                                                    <a
                                                        href={item.href}
                                                        className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                    >
                                                        {item.name}
                                                    </a>
                                                )}
                                            </Menu.Item>
                                        ))}
                                    </Menu.Items>
                                </Transition>
                            </Menu>
                        </div>
                    </div>
                </div>

                <main className="flex-1 relative overflow-y-auto focus:outline-none">
                    <div className="py-6">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>
                        </div>
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                            {/* Replace with your content */}
                            <div className="py-4">
                                <div className="border-4 border-dashed border-gray-200 rounded-lg h-96" />
                            </div>
                            {/* /End replace */}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    )
}
