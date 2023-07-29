import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackForm from "./RiserStackForm";
import useRoute from "../../hooks/useRoute";

const Create = ({ voiceParts, singers }) => {
    const { route } = useRoute();

    return (
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

            <RiserStackForm voiceParts={voiceParts} singers={singers} />
        </>
    );
}

Create.layout = page => <TenantLayout children={page} />

export default Create;