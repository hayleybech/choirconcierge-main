import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import SongFilters from "../../components/Song/SongFilters";
import IndexContainer from "../../components/IndexContainer";

const Index = ({ songs, statuses, categories }) => {
    const [showFilters, setShowFilters] = useState(false);

    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Songs" />
            <PageHeader
                title="Songs"
                icon="list-music"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Songs', url: route('songs.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('songs.create'), variant: 'primary', can: 'create_song' },
                    { label: 'Filter', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                showFilters={showFilters}
                filters={<SongFilters statuses={statuses} categories={categories} onClose={() => setShowFilters(false)} />}
                tableMobile={<SongTableMobile songs={songs} />}
                tableDesktop={<SongTableDesktop songs={songs} />}
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;