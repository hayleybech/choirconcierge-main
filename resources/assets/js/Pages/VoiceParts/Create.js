import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";

const Create = () => (
    <>
        <AppHead title="Create Voice Part" />
        <PageHeader
            title="Create Voice Part"
            icon="fa-users-class"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Singers', url: route('singers.index')},
                { name: 'Voice Parts', url: route('voice-parts.index')},
                { name: 'Create', url: route('voice-parts.create')},
            ]}
        />

        <VoicePartForm />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;