import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";

const RiserStackTableDesktop = ({ stacks }) => (
    <div className="-my-2 overflow-x-auto">
        <div className="py-2 align-middle inline-block min-w-full">
            <div className="shadow overflow-hidden border-b border-gray-200">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Title
                        </th>
                        <th
                            scope="col"
                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Created
                        </th>
                    </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                    {stacks.map((stack) => (
                        <tr key={stack.id}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div className="ml-4">
                                        <Link href={route('stacks.show', stack.id)} className="text-sm font-medium text-purple-800">{stack.title}</Link>
                                    </div>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {DateTime.fromJSDate(new Date(stack.created_at)).toLocaleString(DateTime.DATE_MED)}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
);

export default RiserStackTableDesktop;