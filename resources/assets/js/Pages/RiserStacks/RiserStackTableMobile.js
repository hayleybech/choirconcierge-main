import React from 'react';
import {Link} from "@inertiajs/inertia-react";

const RiserStackTableMobile = ({ stacks }) => (
    <ul className="divide-y divide-gray-200">
        {stacks.map((stack) => (
            <li key={stack.id}>
                <Link href={route('stacks.show', stack.id)} className="block hover:bg-gray-50">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="min-w-0 flex-1 flex items-center">
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div>
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <span className="text-sm font-medium text-indigo-600 truncate">{stack.title}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <i className="fa fa-fw fa-chevron-right text-gray-400" />
                        </div>
                    </div>
                </Link>
            </li>
        ))}
    </ul>
);

export default RiserStackTableMobile;