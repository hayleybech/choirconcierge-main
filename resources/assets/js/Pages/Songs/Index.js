import React from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ songs }) => {
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
                    { label: 'Filter', icon: 'filter', url: '#'},
                ].filter(action => action.can ? can[action.can] : true)}
            />

            {/* Desktop Table */}
            <div className="hidden lg:flex flex-col">
                <SongTableDesktop songs={songs} />
            </div>

            {/* Mobile Table */}
            <div className="bg-white shadow block lg:hidden">
                <SongTableMobile songs={songs} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;