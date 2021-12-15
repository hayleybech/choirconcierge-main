import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import SongFilters from "../../components/Song/SongFilters";

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

            <div className="flex flex-col lg:flex-row">
                {showFilters && (
                    <div className="lg:w-1/5 xl:w-1/6 border-b lg:border-r border-gray-300 lg:z-10">
                        <SongFilters statuses={statuses} categories={categories} onClose={() => setShowFilters(false)} />
                    </div>
                )}
                <div className="flex-grow lg:overflow-x-auto">
                    <div className="hidden lg:flex flex-col overflow-y-hidden">
                        <SongTableDesktop songs={songs} />
                    </div>

                    <div className="bg-white shadow block lg:hidden">
                        <SongTableMobile songs={songs} />
                    </div>
                </div>
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;