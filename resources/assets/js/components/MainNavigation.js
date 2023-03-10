import {Link} from "@inertiajs/inertia-react";
import classNames from "../classNames";
import React from "react";
import Icon from "./Icon";
import useRoute from "../hooks/useRoute";

const MainNavigation = ({ navigation, closeSidebar }) => {
    const { route } = useRoute();

    return (
        <nav className="flex-1 px-4 space-y-1">
            {navigation.map((item) => (
                <div key={item.name}>
                    <Link
                        href={route(item.route)}
                        className={classNames(
                            item.active ? 'bg-purple-800 text-white' : 'text-white text-opacity-80 hover:bg-white hover:bg-opacity-90 hover:text-brand-purple-dark',
                            'group flex items-center px-2 py-2 text-base uppercase font-bold rounded-md'
                        )}
                        onClick={closeSidebar}
                    >
                        <Icon icon={item.icon} className="mr-4" />
                        {item.name}
                    </Link>
                    {item.active && item.items.length > 0 && (
                        <div className="flex flex-col mt-1 mb-3">
                            {item.items.map((child) => (
                                <Link
                                    key={child.name}
                                    href={route(child.route)}
                                    className={classNames(
                                        child.active
                                            ? 'font-semibold bg-black bg-opacity-25'
                                            : 'text-opacity-90 font-light hover:bg-white hover:bg-opacity-90 hover:text-brand-purple-dark',
                                        'px-6 py-1.5 rounded-md text-base text-white'
                                    )}
                                    onClick={closeSidebar}
                                >
                                    <Icon icon={child.icon} type="regular" className="mr-3" />
                                    {child.name}
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            ))}
        </nav>
    );
}

export default MainNavigation;