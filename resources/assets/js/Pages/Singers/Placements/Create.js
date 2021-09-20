import PageHeader from "../../../components/PageHeader";
import Layout from "../../../Layouts/Layout";
import React from "react";
import PlacementForm from "./PlacementForm";

const Create = ({ singer, voice_parts }) => (
    <>
        <PageHeader
            title="Create Placement"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: singer.user.name, url: route('singers.show', singer)},
                { name: 'Create Voice Placement', url: route('singers.placements.create', singer)},
            ]}
        />

        <PlacementForm singer={singer} voice_parts={voice_parts} />
    </>
);

Create.layout = page => <Layout children={page} title="Voice Placements" />

export default Create;