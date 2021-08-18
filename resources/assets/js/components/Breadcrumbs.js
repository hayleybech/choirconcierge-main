import React from 'react';
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/solid";
import {Link} from "@inertiajs/inertia-react";

const Breadcrumbs = ({breadcrumbs}) => (
    <div>
        <nav className="sm:hidden" aria-label="Back">
            <Link href="#" className="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <ChevronLeftIcon className="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
                Back
            </Link>
        </nav>
        <nav className="hidden sm:flex" aria-label="Breadcrumb">
            <ol className="flex items-center space-x-4">
                {breadcrumbs.map((crumb, index) => (
                    <li key={index}>
                        <div className="flex items-center">
                            {index > 0 && (
                                <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                            )}
                            {crumb}
                        </div>
                    </li>
                ))}
            </ol>
        </nav>
    </div>
);

export default Breadcrumbs;