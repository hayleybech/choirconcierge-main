import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackForm from "./RiserStackForm";

const Edit = ({ stack }) => (
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

        <RiserStackForm stack={stack} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;