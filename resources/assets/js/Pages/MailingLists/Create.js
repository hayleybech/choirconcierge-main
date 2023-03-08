import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListForm from "./MailingListForm";

const Create = ({ roles, voiceParts, singerCategories }) => (
    <>
        <AppHead title="Create Mailing List" />
        <PageHeader
            title="Create Mailing List"
            icon="mail-bulk"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Mailing Lists', url: route('groups.index')},
                { name: 'Create', url: route('groups.create')},
            ]}
        />

        <MailingListForm roles={roles} voiceParts={voiceParts} singerCategories={singerCategories} />
    </>
);

Create.layout = page => <TenantLayout children={page} />

export default Create;