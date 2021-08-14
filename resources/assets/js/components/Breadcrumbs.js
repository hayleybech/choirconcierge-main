import React from 'react';
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/solid";
import {Link} from "@inertiajs/inertia-react";

export default function Breadcrumbs() {
    return (
        <div>
            <nav className="sm:hidden" aria-label="Back">
                <Link href="#" className="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <ChevronLeftIcon className="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
                    Back
                </Link>
            </nav>
            <nav className="hidden sm:flex" aria-label="Breadcrumb">
                <ol className="flex items-center space-x-4">
                    <li>
                        <div>
                            <Link href="/" className="text-sm font-medium text-gray-500 hover:text-gray-700">
                                Dashboard
                            </Link>
                        </div>
                    </li>
                    <li>
                        <div className="flex items-center">
                            <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                            <Link href="/singers" className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Singers
                            </Link>
                        </div>
                    </li>
                    <li>
                        <div className="flex items-center">
                            <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                            <Link href="/singers/1" aria-current="page" className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Hayden Bech
                            </Link>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    );
}