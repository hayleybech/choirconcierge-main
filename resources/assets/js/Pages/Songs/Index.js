import React from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";

const Index = ({ songs }) => (
    <>
        <PageHeader
            title="Songs"
            icon="fa-list-music"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Songs', url: route('songs.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'plus', url: ('songs.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: route('voice-parts.index')},
            ]}
        />

        {/* Desktop Table */}
        <div className="hidden lg:flex flex-col">
            <SongTableDesktop songs={songs} />
        </div>

        {/* Mobile Table */}
        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <SongTableMobile songs={songs} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} title="Songs" />

export default Index;