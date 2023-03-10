import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListForm from "./MailingListForm";
import useRoute from "../../hooks/useRoute";

const Edit = ({ list, roles, voiceParts, singerCategories }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title={`Edit - ${list.title}`} />
            <PageHeader
                title="Edit Mailing List"
                icon="fa-mail-bulk"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Mailing Lists', url: route('groups.index')},
                    { name: list.title, url: route('groups.show', {group: list})},
                    { name: 'Edit', url: route('groups.edit', {group: list})},
                ]}
            />

            <MailingListForm list={list} roles={roles} voiceParts={voiceParts} singerCategories={singerCategories} />
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;