import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderForm from "./FolderForm";

const Create = () => (
    <>
        <AppHead title="Create Folder" />
        <PageHeader
            title="Create Folder"
            icon="folders"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Folders', url: route('folders.index')},
                { name: 'Create', url: route('folders.create')},
            ]}
        />

        <FolderForm />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;