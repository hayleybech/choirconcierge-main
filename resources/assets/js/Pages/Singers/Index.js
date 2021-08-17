import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";
import {ChevronRightIcon} from "@heroicons/react/solid";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import VoicePartTag from "../../components/VoicePartTag";

const Index = ({all_singers}) => (
    <>
        <div className="py-6 bg-white border-b border-gray-300">
            <div className=" px-4 sm:px-6 md:px-8">
                <SingerPageHeader
                    title="Singers"
                    icon="fa-users"
                    breadcrumbs={[
                        <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Dashboard
                        </Link>,
                        <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Singers
                        </Link>
                    ]}
                    actions={[

                    ]}
                />
            </div>
        </div>

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
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
                                    Voice Part
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
                                    Email
                                </th>
                                <th scope="col" className="relative px-6 py-3">
                                    <span className="sr-only">Edit</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                            {all_singers.map((singer) => (
                                <tr key={singer.id}>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="flex items-center">
                                            <div className="flex-shrink-0 h-10 w-10">
                                                <img className="h-10 w-10 rounded-md" src={singer.user.avatar_url} alt="" />
                                            </div>
                                            <div className="ml-4">
                                                <Link href={route('singers.show', singer.id)} className="text-sm font-medium text-purple-800">{singer.user.name}</Link>
                                                <div className="text-sm text-gray-500"><i className="fas fa-fw fa-phone mr-1.5" /> {singer.user.phone ?? 'No phone'}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <SingerCategoryTag name={singer.category.name} colour={singer.category.colour} withLabel />
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-gray-500">
                                            <i className="fas fa-fw fa-envelope mr-1.5" />
                                            <span>{singer.user.email}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" className="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <ul className="divide-y divide-gray-200">
                {all_singers.map((singer) => (
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
        </div>
    </>
);

Index.layout = page => <Layout children={page} title="Singers" />

export default Index;