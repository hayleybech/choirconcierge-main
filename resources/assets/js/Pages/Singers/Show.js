import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
import {Link} from "@inertiajs/inertia-react";
import VoicePartTag from "../../components/VoicePartTag";
import SingerCategoryTag from "../../components/SingerCategoryTag";
import moment from "moment";

const Show = ({singer}) => (
    <>
        <SingerPageHeader
            title={singer.user.name}
            image={singer.user.avatar_url}
            meta={(
            <>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    {singer.voice_part && <VoicePartTag title={singer.voice_part.title} colour={singer.voice_part.colour} />}
                </div>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <SingerCategoryTag name={singer.category.name} colour={singer.category.colour} withLabel />
                </div>
                <div className="mt-2 flex items-center text-sm text-gray-500">
                    <i className="far fa-fw fa-calendar-day mr-1.5 text-gray-400 text-md" />
                    Joined {moment(singer.joined_at).format('MMMM D, YYYY')}
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