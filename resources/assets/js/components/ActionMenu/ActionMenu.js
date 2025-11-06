import React from 'react';
import { Fragment } from 'react';
import { Menu, Transition } from '@headlessui/react';
import buttonStyles from '../inputs/buttonStyles';
import Icon from '../Icon';

const ActionMenu = ({ children, optionsVariant }) => (
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
                {children}
            </Menu.Items>
        </Transition>
    </Menu>
);

export default ActionMenu;