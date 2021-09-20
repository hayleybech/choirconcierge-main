import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import SongForm from "./SongForm";
import AppHead from "../../components/AppHead";

const Edit = ({ categories, statuses, pitches, song }) => (
    <>
        <AppHead title={`Edit - ${song.title}`} />
        <PageHeader
            title="Edit Song"
            icon="fa-list-music"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Songs', url: route('songs.index')},
                { name: song.title, url: route('songs.show', song)},
                { name: 'Edit', url: route('songs.edit', song)},
            ]}
        />

        <SongForm categories={categories} statuses={statuses} pitches={pitches} song={song} />
    </>
);

Edit.layout = page => <Layout children={page} title="Songs" />

export default Edit;