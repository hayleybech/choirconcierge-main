import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackForm from "./RiserStackForm";

const Edit = ({ stack, voice_parts }) => (
    <>
        <AppHead title={`Edit - ${stack.title}`} />
        <PageHeader
            title="Edit Riser Stack"
            icon="fa-people-arrows"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Riser Stacks', url: route('stacks.index')},
                { name: stack.title, url: route('stacks.show', stack)},
                { name: 'Edit', url: route('stacks.edit', stack)},
            ]}
        />

        <RiserStackForm stack={stack} voiceParts={voice_parts} />
    </>
);

Edit.layout = page => <TenantLayout children={page} />

export default Edit;