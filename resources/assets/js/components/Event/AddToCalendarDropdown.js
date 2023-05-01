import React, {Fragment} from 'react';
import {Menu, Transition} from "@headlessui/react";
import buttonStyles from "../inputs/buttonStyles";
import Icon from "../Icon";
import classNames from "../../classNames";

const AddToCalendarDropdown = ({ urls }) => {
    let items = [
        { label: 'Google', icon: 'google', iconType: 'brand', url: urls.google },
        { label: 'Outlook Web', icon: 'microsoft', iconType: 'brand', url: urls.webOutlook },
        { label: 'ICS (iCal, Outlook etc)', icon: 'download', iconType: 'solid', url: urls.ics },
    ].filter(item => !!item.url);

    return items.length > 0 && (
        <Menu as="div" className="relative inline-block overflow-visible">
            <Menu.Button className={buttonStyles('secondary', 'sm')}>
                <Icon icon="calendar-plus" />
                Add To Calendar
                <Icon icon="chevron-down" />
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
                    {items.map(({ label, icon, iconType, url }) =>
                        <Menu.Item key={label}>
                            {({ active }) => (
                                <a href={url} className={classNames(
                                    'block w-full text-left px-4 py-2 text-sm',
                                    active ? 'bg-gray-100' : '',
                                )}>
                                    <Icon icon={icon} type={iconType} mr />
                                    {label}
                                </a>
                            )}
                        </Menu.Item>
                    )}
                </Menu.Items>
            </Transition>
        </Menu>
    );
}

export default AddToCalendarDropdown;