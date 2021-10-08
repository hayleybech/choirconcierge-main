import React from 'react';
import {Link} from "@inertiajs/inertia-react";

const RoleTableMobile = ({ roles }) => (
    <ul className="divide-y divide-gray-200">
        {roles.map((role) => (
            <li key={role.id}>
                <Link href={route('roles.edit', role.id)} className="block hover:bg-gray-50">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="min-w-0 flex-1 flex items-center">
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div>
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <span className="text-sm font-medium text-indigo-600 truncate">{role.name}</span>
                                        </p>
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <span className="text-sm font-medium text-sm text-gray-500">
                                                Singers: {role.singers_count}
                                            </span>
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

export default RoleTableMobile;