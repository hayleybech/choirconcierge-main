import React, {Fragment} from 'react';
import Icon from "./Icon";
import {Menu, Transition} from "@headlessui/react";
import classNames from "../classNames";
import {Link, usePage} from "@inertiajs/react";
import useRoute from "../hooks/useRoute";

const LayoutTopBar = ({setShowImpersonateModal, setSidebarOpen, switchChoirMenu}) => {
    const {user, impersonationActive, userNavigation: baseUserNavigation} = usePage().props;
    const {route} = useRoute();

    const actionHandlers = {
        impersonate_modal: () => setShowImpersonateModal(true),
    };

    const userNavigation = baseUserNavigation.map(item =>
        item.action && actionHandlers[item.action]
            ? {...item, action: actionHandlers[item.action]}
            : item
    );

    return (
        <div className="relative z-10 shrink-0 flex h-16 bg-white border-b border-gray-300">
            <button
                type="button"
                className="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 xl:hidden"
                onClick={() => setSidebarOpen(true)}
            >
                <span className="sr-only">Open sidebar</span>
                <Icon icon="bars"/>
            </button>
            <div className="flex-1 pr-4 sm:px-4 flex justify-between">
                <div className="flex-1 flex">
                    <div className="grow sm:grow-0 flex">
                        {switchChoirMenu}
                    </div>
                </div>
                <div className="ml-4 flex items-center lg:ml-6">

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
                            <Menu.Items
                                className="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                {userNavigation.map((item) => (
                                    <Menu.Item key={item.name}>
                                        {({active}) => (
                                            <>
                                                {item.hide || (
                                                    <>
                                                        {typeof item.action === 'function' ? (
                                                            <button
                                                                onClick={item.action}
                                                                type="button"
                                                                className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                            >
                                                                <Icon icon={item.icon} mr/>
                                                                {item.name}
                                                            </button>
                                                        ) : item.external ? (
                                                            <a
                                                                href={item.route}
                                                                className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                            >
                                                                <Icon icon={item.icon} mr/>
                                                                {item.name}
                                                            </a>
                                                        ) : (
                                                            <Link
                                                                href={route(item.route, item.params)}
                                                                as={item.method ? 'button' : 'a'}
                                                                method={item.method}
                                                                className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                                            >
                                                                <Icon icon={item.icon} mr/>
                                                                {item.name}
                                                            </Link>
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
