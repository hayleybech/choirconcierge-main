import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SongStatusTag from "../../components/SongStatusTag";
import PitchButton from "../../components/PitchButton";
import {DateTime} from "luxon";
import Swatch from "../../components/Swatch";

const VoicePartTableDesktop = ({ voiceParts }) => (
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
                            Singers
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
                    {voiceParts.map((voicePart) => (
                        <tr key={voicePart.id}>
                            <td className="px-6 py-4 whitespace-nowrap">
                                <div className="flex items-center">
                                    <div>
                                        <Swatch colour={voicePart.colour} />
                                    </div>
                                    <div className="ml-4">
                                        <Link href={route('voice-parts.edit', voicePart.id)} className="text-sm font-medium text-purple-800">{voicePart.title}</Link>
                                    </div>
                                </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {voicePart.singers_count}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {DateTime.fromJSDate(new Date(voicePart.created_at)).toLocaleString(DateTime.DATE_MED)}
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
);

export default VoicePartTableDesktop;