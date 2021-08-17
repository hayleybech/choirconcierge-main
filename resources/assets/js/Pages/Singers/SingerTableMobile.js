import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import VoicePartTag from "../../components/VoicePartTag";
import {ChevronRightIcon} from "@heroicons/react/solid";

const SingerTableMobile = ({ singers }) => (
    <ul className="divide-y divide-gray-200">
        {singers.map((singer) => (
            <li key={singer.id}>
                <Link href={route('singers.show', singer.id)} className="block hover:bg-gray-50">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="min-w-0 flex-1 flex items-center">
                            <div className="flex-shrink-0">
                                <img className="h-12 w-12 rounded-lg" src={singer.user.avatar_url} alt={singer.user.name} />
                            </div>
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div>
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <SingerCategoryTag name={singer.category.name} colour={singer.category.colour} />
                                            <span className="text-sm font-medium text-indigo-600 truncate">{singer.user.name}</span>
                                        </p>
                                        {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
                                            <i className="fas fa-fw fa-phone mr-1.5" />
                                            <span className="truncate">{singer.user.phone ?? 'No phone'}</span>
                                        </p>

                                        <p className="mt-2 hidden sm:flex items-center text-sm text-gray-500 min-w-0">
                                            <i className="fas fa-fw fa-envelope mr-1.5" />
                                            <span className="truncate">{singer.user.email}</span>
                                        </p>
                                    </div>
                                </div>
                                {/*<div className="hidden md:block">*/}
                                {/*    <div>*/}
                                {/*        <p className="text-sm text-gray-900">*/}
                                {/*            /!*Applied on <time dateTime={application.date}>{application.dateFull}</time>*!/*/}
                                {/*        </p>*/}
                                {/*        <p className="mt-2 flex items-center text-sm text-gray-500">*/}
                                {/*            <CheckCircleIcon className="flex-shrink-0 mr-1.5 h-5 w-5 text-green-400" aria-hidden="true" />*/}
                                {/*            /!*{application.stage}*!/*/}
                                {/*        </p>*/}
                                {/*    </div>*/}
                                {/*</div>*/}
                            </div>
                        </div>
                        <div>
                            <ChevronRightIcon className="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                    </div>
                </Link>
            </li>
        ))}
    </ul>
);

export default SingerTableMobile;