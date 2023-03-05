import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import React from "react";
import PlacementForm from "./PlacementForm";
import AppHead from "../../../components/AppHead";

const Create = ({ singer, voice_parts }) => (
    <>
        <AppHead title={`Add Voice Placement - ${singer.user.name}`} />
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

Create.layout = page => <TenantLayout children={page} />

export default Create;