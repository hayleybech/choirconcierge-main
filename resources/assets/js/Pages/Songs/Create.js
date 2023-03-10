import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import SongForm from "./SongForm";
import AppHead from "../../components/AppHead";
import useRoute from "../../hooks/useRoute";

const Create = ({ categories, statuses, pitches }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Create Song" />
            <PageHeader
                title="Create Song"
                icon="fa-list-music"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Songs', url: route('songs.index')},
                    { name: 'Create', url: route('songs.create')},
                ]}
            />

            <SongForm categories={categories} statuses={statuses} pitches={pitches} />
        </>
    );
}

Create.layout = page => <TenantLayout children={page} />

export default Create;