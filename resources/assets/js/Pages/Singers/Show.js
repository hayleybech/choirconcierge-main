import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {BriefcaseIcon, CalendarIcon, CurrencyDollarIcon, LocationMarkerIcon} from "@heroicons/react/solid";
import {Link} from "@inertiajs/inertia-react";

const Show = ({singer}) => (
    <>
        <SingerPageHeader
            title={singer.user.name}
            icon="fa-users"
            meta={(
            <>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <BriefcaseIcon className="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                    Full-time
                </div>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <LocationMarkerIcon className="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                    Remote
                </div>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <CurrencyDollarIcon className="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                    $120k &ndash; $140k
                </div>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <CalendarIcon className="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                    Closing on January 9, 2020
                </div>
            </>)}
            breadcrumbs={[
                <Link href={route('dash')} className="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Dashboard
                </Link>,
                <Link href={route('singers.index')} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Singers
                </Link>,
                <Link href={route('singers.show', singer.id)} className="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">
                    {singer.user.name}
                </Link>
            ]}
        />
    </>
);

Show.layout = page => <Layout children={page} title="Singers" />

export default Show;