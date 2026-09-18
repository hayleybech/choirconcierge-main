import React, {Fragment} from "react";
import {Link, usePage} from "@inertiajs/react";
import {Menu, Transition} from "@headlessui/react";
import classNames from "../classNames";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";

const UserMenuItems = ({onClose, setShowImpersonateModal}) => {
    const {userNavigation: baseUserNavigation} = usePage().props;
    const {route} = useRoute();

    const actionHandlers = {
        impersonate_modal: () => setShowImpersonateModal?.(true),
    };

    const userNavigation = baseUserNavigation.map(item =>
        item.action && actionHandlers[item.action]
            ? {...item, action: actionHandlers[item.action]}
            : item
    );

    return userNavigation.map(item => {
        if (item.hide) {
            return null;
        }

        const itemClassName = classNames(
            'flex w-full items-center px-4 py-2 text-sm text-gray-700 text-left',
            'hover:bg-gray-100'
        );

        if (typeof item.action === 'function') {
            return (
                <button key={item.name} onClick={() => { item.action(); onClose?.(); }} type="button" className={itemClassName}>
                    <Icon icon={item.icon} mr />
                    {item.name}
                </button>
            );
        }

        if (item.external) {
            return (
                <a key={item.name} href={item.route} className={itemClassName} onClick={onClose}>
                    <Icon icon={item.icon} mr />
                    {item.name}
                </a>
            );
        }

        return (
            <Link
                key={item.name}
                href={route(item.route, item.params)}
                as={item.method ? 'button' : 'a'}
                method={item.method}
                className={itemClassName}
                onClick={onClose}
            >
                <Icon icon={item.icon} mr />
                {item.name}
            </Link>
        );
    });
};

const UserMenu = ({mobile = false, onOpen, onBack, onClose, setShowImpersonateModal}) => {
    const {user, impersonationActive} = usePage().props;

    if (mobile && !onOpen) {
        return (
            <div className="flex h-full flex-col">
                <div className="flex items-center gap-3 border-b border-white border-opacity-20 px-4 py-4 text-gray-700">
                    <button type="button" onClick={onBack} className="flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-gray-700">
                        <span className="sr-only">Back to main menu</span>
                        <Icon icon="arrow-left" type="light" />
                    </button>
                    <span className="text-base font-bold uppercase">Account</span>
                </div>
                <div className="flex items-center gap-3 px-4 py-5 text-gray-700">
                    <img className="h-10 w-10 rounded-lg" src={user.avatar_url} alt={user.name} />
                    <div className="min-w-0">
                        <p className="truncate font-semibold">{user.name}</p>
                        <p className="truncate text-sm text-gray-600 text-opacity-75">{user.email}</p>
                    </div>
                </div>
                <div className="flex flex-col divide-y divide-gray-200 bg-white py-1">
                    <UserMenuItems onClose={onClose} setShowImpersonateModal={setShowImpersonateModal} />
                </div>
            </div>
        );
    }

    return (
        <Menu as="div" className="relative mt-4">
            <Menu.Button
                onClick={onOpen}
                className={classNames(
                    'flex w-full items-center gap-3 rounded-md px-2 py-3 text-left text-white hover:bg-white hover:bg-opacity-90 hover:text-brand-purple-dark focus:outline-none focus:ring-2 focus:ring-white',
                    impersonationActive ? 'border-2 border-red-500' : ''
                )}
            >
                <img className="h-10 w-10 shrink-0 rounded-lg" src={user.avatar_url} alt={user.name} />
                <span className="min-w-0 flex-1">
                    <span className="block truncate text-sm font-semibold">{user.name}</span>
                    <span className="block truncate text-xs text-current text-opacity-75">{user.email}</span>
                </span>
                <Icon icon="chevron-up" className="shrink-0" />
            </Menu.Button>
            <Transition as={Fragment} enter="transition ease-out duration-100" enterFrom="opacity-0 scale-95" enterTo="opacity-100 scale-100" leave="transition ease-in duration-75" leaveFrom="opacity-100 scale-100" leaveTo="opacity-0 scale-95">
                <Menu.Items className="absolute bottom-full left-0 z-20 mb-2 w-full origin-bottom-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <UserMenuItems setShowImpersonateModal={setShowImpersonateModal} />
                </Menu.Items>
            </Transition>
        </Menu>
    );
};

export default UserMenu;
