import React from 'react';
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/solid";

export default function Breadcrumbs() {
    return (
        <div>
            <nav className="sm:hidden" aria-label="Back">
                <a href="#" className="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                    <ChevronLeftIcon className="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" aria-hidden="true" />
                    Back
                </a>
            </nav>
            <nav className="hidden sm:flex" aria-label="Breadcrumb">
                <ol className="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="/" className="text-sm font-medium text-gray-500 hover:text-gray-700">
                                Dashboard
                            </a>
                        </div>
                    </li>
                    <li>
                        <div className="flex items-center">
                            <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                            <a href="#" className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Singers
                            </a>
                        </div>
                    </li>
                    <li>
                        <div className="flex items-center">
                            <ChevronRightIcon className="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true" />
                            <a href="#" aria-current="page" className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Hayden Bech
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    );
}