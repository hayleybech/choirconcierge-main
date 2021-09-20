import PageHeader from "../../../components/PageHeader";
import Layout from "../../../Layouts/Layout";
import React from "react";
import PlacementForm from "./PlacementForm";

const Edit = ({ singer, placement, voice_parts }) => (
    <>
        <PageHeader
            title="Edit Placement"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: singer.user.name, url: route('singers.show', singer)},
                { name: 'Edit Voice Placement', url: route('singers.placements.edit', [singer, placement])},
            ]}
        />

        <PlacementForm singer={singer} placement={placement} voice_parts={voice_parts} />
    </>
);

Edit.layout = page => <Layout children={page} title="Voice Placements" />

export default Edit;