import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackForm from "./RiserStackForm";
import useRoute from "../../hooks/useRoute";

const Edit = ({ stack, voiceParts, singers }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`Edit - ${stack.title}`} />
            <PageHeader
                title="Edit Riser Stack"
                icon="fa-people-arrows"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Riser Stacks', url: route('stacks.index')},
                    { name: stack.title, url: route('stacks.show', {stack})},
                    { name: 'Edit', url: route('stacks.edit', {stack})},
                ]}
            />

            <RiserStackForm stack={stack} voiceParts={voiceParts} singers={singers} />
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;