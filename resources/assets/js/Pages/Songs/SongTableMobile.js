import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import VoicePartTag from "../../components/VoicePartTag";
import PitchButton from "../../components/PitchButton";
import SongStatusTag from "../../components/SongStatusTag";
import {DateTime} from "luxon";

const SongTableMobile = ({ songs }) => (
    <ul className="divide-y divide-gray-200">
        {songs.map((song) => (
            <li key={song.id}>
                <Link href={route('singers.show', song.id)} className="block hover:bg-gray-50">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="min-w-0 flex-1 flex items-center">
                            <div className="flex-shrink-0">
                                <PitchButton note={song.pitch.split('/')[0]} />
                            </div>
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div>
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <SongStatusTag name={song.status.title} colour={song.status.colour} />
                                            <span className="text-sm font-medium text-indigo-600 truncate">{song.title}</span>
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

export default SongTableMobile;