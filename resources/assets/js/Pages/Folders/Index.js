import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderTableDesktop from "./FolderTableDesktop";
import FolderTableMobile from "./FolderTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import DeleteDialog from "../../components/DeleteDialog";

const Index = ({ folders }) => {
    const { can } = usePage().props;

    const [deletingFolder, setDeletingFolder] = useState(null);
    const [deletingDocument, setDeletingDocument] = useState(null);

    return (
        <>
            <AppHead title="Documents" />
            <PageHeader
                title="Documents"
                icon="folders"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Documents', url: route('folders.index')},
                ]}
                actions={[
                    { label: 'Add Folder', icon: 'folder-plus', url: route('folders.create'), variant: 'primary', can: 'create_folder' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <div className="hidden lg:flex flex-col">
                <FolderTableDesktop folders={folders} setDeletingFolder={setDeletingFolder} setDeletingDocument={setDeletingDocument} />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <FolderTableMobile folders={folders} />
            </div>

            <DeleteDialog title="Delete Folder" url={deletingFolder ? route('folders.destroy', deletingFolder) : '#'} isOpen={!!deletingFolder} setIsOpen={setDeletingFolder}>
                Are you sure you want to delete this folder?
                All of its documents will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>

            <DeleteDialog
                title="Delete Document"
                url={deletingDocument ? route('folders.documents.destroy', [deletingDocument.folder_id, deletingDocument]) : '#'}
                isOpen={!!deletingDocument}
                setIsOpen={setDeletingDocument}
            >
                Are you sure you want to delete this document?
                It will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;