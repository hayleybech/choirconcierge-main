import React from 'react';
import {Link} from "@inertiajs/react";
import Icon from "./Icon";

const Breadcrumbs = ({ breadcrumbs }) => {
    const parentCrumb = breadcrumbs[breadcrumbs.length - 2] ?? breadcrumbs[0];

    return (
        <div>
            <nav className="sm:hidden" aria-label="Back">
                <Link
                    href={parentCrumb.url}
                    className="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
                >
                    <Icon icon="chevron-left" className="text-gray-400 text-xs -ml-1 mr-1" />
                    {parentCrumb.name}
                </Link>
            </nav>
            <nav className="hidden sm:flex" aria-label="Breadcrumb">
                <ol className="flex items-center space-x-4">
                    {breadcrumbs.map(({name, url}, index) => (
                        <li key={index}>
                            <div className="flex items-center">
                                {index > 0 && (
                                    <Icon icon="chevron-right" className="text-gray-400 text-xs mr-4" />
                                )}
                                <Link href={url} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                                    {name}
                                </Link>
                            </div>
                        </li>
                    ))}
                </ol>
            </nav>
        </div>
    );
}

export default Breadcrumbs;