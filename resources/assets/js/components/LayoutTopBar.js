import React, {Fragment} from 'react';
import Icon from "./Icon";
import route from "ziggy-js";
import {Menu, Transition} from "@headlessui/react";
import classNames from "../classNames";
import {Link, usePage} from "@inertiajs/inertia-react";

const LayoutTopBar = ({ setShowImpersonateModal, setSidebarOpen }) => {
    const { can, user, impersonationActive } = usePage().props;

    const userNavigation = [
        // { name: 'Edit Profile', href: route('accounts.edit'), icon: 'user-edit' },
        // { name: 'Impersonate User', action: () => setShowImpersonateModal(true), icon: 'user-unlock', hide: !can.impersonate || impersonationActive },
        // { name: 'Stop Impersonating', href: route('impersonation.stop'), icon: 'user-lock', hide: !impersonationActive },
        { name: 'Sign out', href: route('logout'), method: 'POST', icon: 'sign-out-alt' }
    ];

    return (
        <div className="relative z-10 shrink-0 flex h-16 bg-white border-b border-gray-300">
            <button
                type="button"
                className="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 xl:hidden"
                onClick={() => setSidebarOpen(true)}
            >
                <span className="sr-only">Open sidebar</span>
                <Icon icon="bars" />
            </button>
            <div className="flex-1 px-4 flex justify-between">
                <div className="flex-1 flex">
                    {/*<form className="w-full flex lg:ml-0" action="#" method="GET">*/}
                    {/*    <label htmlFor="search-field" className="sr-only">*/}
                    {/*        Search*/}
                    {/*    </label>*/}
                    {/*    <div className="relative w-full text-gray-400 focus-within:text-gray-600">*/}
                    {/*        <div className="absolute inset-y-0 left-0 flex items-center pointer-events-none">*/}
                    {/*            <SearchIcon className="h-5 w-5" aria-hidden="true" />*/}
                    {/*        </div>*/}
                    {/*        <input*/}
                    {/*            id="search-field"*/}
                    {/*            className="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm"*/}
                    {/*            placeholder="Search"*/}
                    {/*            type="search"*/}
                    {/*            name="search"*/}
                    {/*        />*/}
                    {/*    </div>*/}
                    {/*</form>*/}
                </div>
                <div className="ml-4 flex items-center lg:ml-6">

                    <a href="https://leanbe.ai/Choir%20Concierge/" target="_blank" className="text-gray-500 text-sm mx-2">
                        <Icon icon="bullhorn" mr />
                        What's New
                    </a>

                    {/* Profile dropdown */}
                    <Menu as="div" className="ml-3 relative">
                        <div>
                            <Menu.Button
                                className={classNames(
                                    'max-w-xs bg-white flex items-center text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500',
                                    impersonationActive ? 'border-2 border-red-500' : '',
                                )}
                            >
                                <span className="sr-only">Open user menu</span>
                                <img
                                    className="h-8 w-8 rounded-lg"
                                    src={user.avatar_url}
                                    alt={user.name}
                                />
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
                                {userNavigation.filter(item => item).map((item) => (
                                    <Menu.Item key={item.name}>
                                        {({ active }) => (
                                            <>
                                                {item.hide || (
                                                    <>
                                                        {item.href ? (
                                                            <Link
                                                                href={item.href}
                                                                as={item.method ? 'button' : 'a'}
                                                                method={item.method}
                                                                className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                            >
                                                                <Icon icon={item.icon} mr />
                                                                {item.name}
                                                            </Link>
                                                        ) : (
                                                            <button
                                                                onClick={item.action}
                                                                type="button"
                                                                className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                            >
                                                                <Icon icon={item.icon} mr />
                                                                {item.name}
                                                            </button>
                                                        )}
                                                    </>
                                                )}
                                            </>
                                        )}
                                    </Menu.Item>
                                ))}
                            </Menu.Items>
                        </Transition>
                    </Menu>
                </div>
            </div>
        </div>
    );
}

export default LayoutTopBar;