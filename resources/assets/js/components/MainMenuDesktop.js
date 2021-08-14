import {Link} from "@inertiajs/inertia-react";
import route from "ziggy-js";
import React from "react";
import classNames from "../classnames";

const MainMenuDesktop = ({navigation}) => (
    <div className="flex flex-col w-64 bg-brand-purple-dark">
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
                        <div key={item.name}>
                            <Link
                                href={route(item.route)}
                                className={classNames(
                                    item.active ? 'bg-indigo-50 text-brand-purple-dark' : 'text-indigo-200 hover:bg-purple-800 hover:text-purple-100',
                                    'group flex items-center px-2 py-2 text-base uppercase font-black rounded-md'
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
                                                'px-6 py-1.5 rounded-md text-base text-indigo-50'
                                            )}
                                        >
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
);

export default MainMenuDesktop;