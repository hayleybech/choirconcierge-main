import React from 'react'
import Layout from "../../Layouts/Layout";
import SingerPageHeader from "./SingerPageHeader";
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
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: singer.user.name, url: route('singers.show', singer) },
            ]}
        />
    </>
);

Show.layout = page => <Layout children={page} title="Singers" />

export default Show;