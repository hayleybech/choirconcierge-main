import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackForm from "./RiserStackForm";

const Create = ({ voice_parts }) => (
    <>
        <AppHead title="Create Riser Stack" />
        <PageHeader
            title="Create Riser Stack"
            icon="fa-people-arrows"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Riser Stacks', url: route('stacks.index')},
                { name: 'Create', url: route('stacks.create')},
            ]}
        />

        <RiserStackForm voiceParts={voice_parts} />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;