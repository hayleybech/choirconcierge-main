import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";

const Edit = ({ voice_part: voicePart }) => (
    <>
        <AppHead title={`Edit - ${voicePart.title}`} />
        <PageHeader
            title="Edit Voice Part"
            icon="fa-list-music"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Voice Parts', url: route('voice-parts.index')},
                { name: voicePart.title, url: '#'},
                { name: 'Edit', url: route('voice-parts.edit', voicePart)},
            ]}
        />

        <VoicePartForm voicePart={voicePart} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;