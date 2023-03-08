import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";

const Edit = ({ voice_part: voicePart }) => (
    <>
        <AppHead title={`Edit - ${voicePart.title}`} />
        <PageHeader
            title="Edit Voice Part"
            icon="fa-users-class"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: 'Voice Parts', url: route('voice-parts.index')},
                { name: voicePart.title, url: '#'},
                { name: 'Edit', url: route('voice-parts.edit', voicePart)},
            ]}
        />

        <VoicePartForm voicePart={voicePart} />
    </>
);

Edit.layout = page => <TenantLayout children={page} />

export default Edit;