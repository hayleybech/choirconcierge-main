import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Swatch from "../../components/Swatch";

const VoicePartTableMobile = ({ voiceParts }) => (
    <ul className="divide-y divide-gray-200">
        {voiceParts.map((voicePart) => (
            <li key={voicePart.id}>
                <Link href={route('voice-parts.edit', voicePart.id)} className="block hover:bg-gray-50">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="min-w-0 flex-1 flex items-center">
                            <div className="flex-shrink-0">
                                <Swatch colour={voicePart.colour} />
                            </div>
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div>
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <span className="text-sm font-medium text-indigo-600 truncate">{voicePart.title}</span>
                                        </p>
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <span className="text-sm font-medium text-sm text-gray-500">
                                                Singers: {voicePart.singers_count}
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

export default VoicePartTableMobile;