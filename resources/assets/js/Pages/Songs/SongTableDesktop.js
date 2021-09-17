import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import {DateTime} from "luxon";

const SongTableDesktop = ({ songs }) => (
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
                            Name
                        </th>
                        <th
                            scope="col"
                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Status
                        </th>
                        <th
                            scope="col"
                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Category
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
                    {songs.map((song) => (
                        <tr key={song.id}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div>
                                        <PitchButton note={song.pitch.split('/')[0]} size="sm" />
                                    </div>
                                    <div className="ml-4">
                                        <Link href={route('songs.show', song.id)} className="text-sm font-medium text-purple-800">{song.title}</Link>
                                    </div>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <SongStatusTag name={song.status.title} colour={song.status.colour} withLabel />
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {song.categories.map(category => (
                                    <span
                                        key={category.id}
                                        className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800 mr-1.5 mb-1.5"
                                    >
                                        {category.title}
                                    </span>
                                ))}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {DateTime.fromJSDate(new Date(song.created_at)).toLocaleString(DateTime.DATE_MED)}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
);

export default SongTableDesktop;