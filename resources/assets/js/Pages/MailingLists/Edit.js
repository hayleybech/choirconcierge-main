import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import MailingListForm from "./MailingListForm";

const Edit = ({ list, roles, voiceParts, singerCategories }) => (
    <>
        <AppHead title={`Edit - ${list.title}`} />
        <PageHeader
            title="Edit Mailing List"
            icon="fa-mail-bulk"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Mailing Lists', url: route('groups.index')},
                { name: list.title, url: route('groups.show', list)},
                { name: 'Edit', url: route('groups.edit', list)},
            ]}
        />

        <MailingListForm list={list} roles={roles} voiceParts={voiceParts} singerCategories={singerCategories} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;