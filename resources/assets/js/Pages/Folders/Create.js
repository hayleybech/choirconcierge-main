import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderForm from "./FolderForm";
import useRoute from "../../hooks/useRoute";

const Create = () => {
    const { route } = useRoute();

    return (
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
}

Create.layout = page => <TenantLayout children={page} />

export default Create;