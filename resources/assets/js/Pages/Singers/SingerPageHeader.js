import React from 'react';
import { Fragment } from 'react'
import { ChevronDownIcon } from '@heroicons/react/solid'
import { Menu, Transition } from '@headlessui/react'
import Breadcrumbs from "../../components/Breadcrumbs";
import ButtonLink from "../../components/inputs/ButtonLink";
import classNames from '../../classNames';
import buttonStyles from "../../components/inputs/buttonStyles";

const SingerPageHeader = ({title, image, icon, meta, breadcrumbs, actions = []}) => (
    <div className="py-6 bg-white border-b border-gray-300">
        <div className=" px-4 sm:px-6 md:px-8">
            <div className="lg:flex lg:items-center lg:justify-between">
                {image && <img src={image} alt={title} className="h-32 lg:h-24 rounded-md mb-3 mr-6" />}
                <div className="flex-1 min-w-0">
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                    <h2 className="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {icon && <i className={'fas fa-fw mr-2 '+icon} />}
                        <span>{title}</span>
                    </h2>
                    <div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        {meta}
                    </div>
                </div>
                <div className="mt-5 flex sm:flex-row-reverse lg:mt-0 lg:ml-4">
                    {actions.map((action, key) => key === 0
                        ?   <span className="sm:ml-3" key={key}>
                                <ButtonLink href={route(action.route)} primary>
                                  <i className={"fa fa-fw -ml-1 mr-2 fa-"+action.icon} />
                                  {action.label}
                                </ButtonLink>
                            </span>
                        :   <span className="hidden sm:block ml-3" key={key}>
                              <ButtonLink href={route(action.route)}>
                                  <i className={"fa fa-fw -ml-1 mr-2 text-gray-500 fa-"+action.icon} />
                                  {action.label}
                              </ButtonLink>
                            </span>
                    )}

                    {/* Dropdown */}
                    <Menu as="span" className="ml-3 relative sm:hidden">
                        <Menu.Button className={buttonStyles()}>
                            More
                            <ChevronDownIcon className="-mr-1 ml-2 h-5 w-5 text-gray-500" aria-hidden="true" />
                        </Menu.Button>

                        <Transition
                            as={Fragment}
                            enter="transition ease-out duration-200"
                            enterFrom="transform opacity-0 scale-95"
                            enterTo="transform opacity-100 scale-100"
                            leave="transition ease-in duration-75"
                            leaveFrom="transform opacity-100 scale-100"
                            leaveTo="transform opacity-0 scale-95"
                        >
                            <Menu.Items className="origin-top-right absolute right-0 mt-2 -mr-1 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <Menu.Item>
                                    {({ active }) => (
                                        <a
                                            href="#"
                                            className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                        >
                                            Edit
                                        </a>
                                    )}
                                </Menu.Item>
                                <Menu.Item>
                                    {({ active }) => (
                                        <a
                                            href="#"
                                            className={classNames(active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700')}
                                        >
                                            View
                                        </a>
                                    )}
                                </Menu.Item>
                            </Menu.Items>
                        </Transition>
                    </Menu>
                </div>
            </div>
        </div>
    </div>
);

export default SingerPageHeader;