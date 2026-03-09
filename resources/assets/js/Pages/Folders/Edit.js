import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import FolderForm from "./FolderForm";
import useRoute from "../../hooks/useRoute";

const Edit = ({ folder, ensembles }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Edit Folder" />
            <PageHeader
                title="Edit Folder"
                icon="folders"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Folders', url: route('folders.index')},
                    { name: 'Edit', url: route('folders.edit', { folder })},
                ]}
            />

            <FolderForm folder={folder} ensembles={ensembles} />
        </>
    );
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;
