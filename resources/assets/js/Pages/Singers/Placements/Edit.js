import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import React from "react";
import PlacementForm from "./PlacementForm";
import AppHead from "../../../components/AppHead";
import useRoute from "../../../hooks/useRoute";

const Edit = ({ singer, placement, voice_parts }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`Edit Voice Placement - ${singer.user.name}`} />
            <PageHeader
                title="Edit Placement"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                    { name: singer.user.name, url: route('singers.show', {singer})},
                    { name: 'Edit Voice Placement', url: route('singers.placements.edit', {singer, placement})},
                ]}
            />

            <PlacementForm singer={singer} placement={placement} voice_parts={voice_parts} />
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;