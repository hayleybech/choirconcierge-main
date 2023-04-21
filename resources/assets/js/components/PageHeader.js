import React from 'react';
import { Fragment } from 'react'
import { Menu, Transition } from '@headlessui/react'
import Breadcrumbs from "./Breadcrumbs";
import classNames from '../classNames';
import buttonStyles from "./inputs/buttonStyles";
import Button from "./inputs/Button";
import Icon from "./Icon";
import {Link} from "@inertiajs/react";

const PageHeader = ({ title, image, icon, meta = [], breadcrumbs, actions = [], optionsVariant }) => (
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
                    <div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        {meta.map((item, key) => (
                            <div className="mt-2 flex items-center text-sm text-gray-500" key={key}>
                                {item}
                            </div>
                        ))}
                    </div>
                </div>
                <div className="mt-5 flex sm:flex-row-reverse lg:mt-0 lg:ml-4">
                    {actions.map((action, key) => (
                        <span className={key === 0 ? 'sm:ml-3' : 'hidden sm:block ml-3'} key={key}>
                            {action.label
                                ? (
                                    <Button href={action.url} onClick={action.onClick} variant={action.variant}>
                                        <Icon icon={action.icon} mr />{action.label}
                                    </Button>
                                ): (
                                    action
                                )
                            }
                        </span>
                    ))}

                    {/* Dropdown */}
                    {actions.length > 2
                        ? (
                        <Menu as="span" className="ml-3 relative sm:hidden z-20">
                            <Menu.Button className={buttonStyles(optionsVariant)}>
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
                                    {actions.map((action, key) =>
                                        key > 0 && (
                                        <Menu.Item key={key}>
                                            {({ active }) => action.url
                                                ? (
                                                    <Link
                                                        href={action.url}
                                                        className={classNames(
                                                            'block w-full text-left px-4 py-2 text-sm',
                                                            active ? 'bg-gray-100' : '',
                                                            action.variant === 'danger-outline' ? 'text-red-500' : 'text-gray-700'
                                                        )}
                                                    >
                                                        <Icon icon={action.icon} mr/>
                                                        {action.label}
                                                    </Link>
                                                ) : (
                                                    <button
                                                        onClick={action.onClick}
                                                        className={classNames(
                                                            'block w-full text-left px-4 py-2 text-sm',
                                                            active ? 'bg-gray-100' : '',
                                                            action.variant === 'danger-outline' ? 'text-red-500' : '',
                                                            action.variant === 'success-solid' ? 'text-emerald-500' : '',
                                                            action.variant !== 'danger-outline' && action.variant !== 'success-solid' ? 'text-gray-700' : '',
                                                        )}
                                                    >
                                                        <Icon icon={action.icon} mr/>
                                                        {action.label}
                                                    </button>
                                                )
                                            }
                                        </Menu.Item>
                                        )
                                    )}
                                </Menu.Items>
                            </Transition>
                        </Menu>
                        )
                        : actions.length === 2 && (
                            <Button href={actions[1].url} onClick={actions[1].onClick} variant={actions[1].variant} className="ml-3 sm:hidden">
                                <Icon icon={actions[1].icon} mr />{actions[1].label}
                            </Button>
                        )
                    }
                </div>
            </div>
        </div>
    </div>
);

export default PageHeader;
