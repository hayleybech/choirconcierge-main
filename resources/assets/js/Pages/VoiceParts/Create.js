import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import VoicePartForm from "./VoicePartForm";
import useRoute from "../../hooks/useRoute";

const Create = () => {
    const { route } = useRoute();

    return (
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
}

Create.layout = page => <TenantLayout children={page} />

export default Create;