import {Dialog, Transition} from "@headlessui/react";
import React, {Fragment} from "react";
import {XIcon} from "@heroicons/react/outline";
import {Link} from "@inertiajs/inertia-react";
import route from "ziggy-js";
import classNames from "../classnames";

const MainMenuMobile = ({navigation, open, setOpen}) => (
    <Transition.Root show={open} as={Fragment}>
        <Dialog as="div" className="fixed inset-0 flex z-40 md:hidden" onClose={setOpen}>
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
                                onClick={() => setOpen(false)}
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
                                <div key={item.name}>
                                    <Link
                                        href={route(item.route)}
                                        className={classNames(
                                            item.active ? 'bg-indigo-50 text-brand-purple-dark' : 'text-indigo-200 hover:bg-purple-800 hover:text-purple-100',
                                            'group flex items-center px-2 py-2 text-lg font-black uppercase rounded-md'
                                        )}
                                    >
                                        <i className={"fas fa-fw mr-4 " + item.icon} aria-hidden="true" />
                                        {item.name}
                                    </Link>
                                    {item.active && item.items.length > 0 && (
                                        <div className="flex flex-col mt-1 mb-3">
                                            {item.items.map((child) => (
                                                <Link
                                                    key={child.name}
                                                    href={route(child.route)}
                                                    className={classNames(
                                                        child.active ? 'font-semibold bg-indigo-50 bg-opacity-10' : 'font-light hover:bg-purple-800 hover:text-purple-50',
                                                        'px-6 py-1.5 rounded-md text-indigo-50'
                                                    )}
                                                >
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
)
export default MainMenuMobile;