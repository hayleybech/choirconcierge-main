import React from 'react'
import Layout from "../../Layouts/Layout";
import SongTableDesktop from "./SongTableDesktop";
import SongTableMobile from "./SongTableMobile";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";

const Index = ({ songs }) => (
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
                { label: 'Add New', icon: 'plus', url: route('songs.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: '#'},
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

Index.layout = page => <Layout children={page} />

export default Index;