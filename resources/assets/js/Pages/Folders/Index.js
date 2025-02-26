import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderTableDesktop from "./FolderTableDesktop";
import FolderTableMobile from "./FolderTableMobile";
import {usePage} from "@inertiajs/react";
import DeleteDialog from "../../components/DeleteDialog";
import EmptyState from "../../components/EmptyState";
import IndexContainer from "../../components/IndexContainer";
import useRoute from "../../hooks/useRoute";

const Index = ({ folders }) => {
    const { can } = usePage().props;
    const { route } = useRoute();

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

            <IndexContainer
                tableDesktop={<FolderTableDesktop folders={folders} setDeletingFolder={setDeletingFolder} setDeletingDocument={setDeletingDocument} permissions={can} />}
                tableMobile={<FolderTableMobile folders={folders} setDeletingFolder={setDeletingFolder} setDeletingDocument={setDeletingDocument} permissions={can} />}
                emptyState={folders.length === 0
                    ? <EmptyState
                        title="No folders"
                        description="Looks like you don't have any folders or documents yet. This is a great place to store meeting minutes, your constitution, or other important files."
                        actionDescription={can['create_folder'] ? 'Get started by adding a folder, then upload some documents to the folder.' : null}
                        icon="folders"
                        href={can['create_folder'] ? route('folders.create') : null}
                        actionLabel="Add Folder"
                        actionIcon="folder-plus"
                    />
                    : null
                }
            />

            <DeleteDialog title="Delete Folder" url={deletingFolder ? route('folders.destroy', {folder: deletingFolder}) : '#'} isOpen={!!deletingFolder} setIsOpen={setDeletingFolder}>
                Are you sure you want to delete this folder?
                All of its documents will be permanently removed from our servers forever.
                This action cannot be undone.
            </DeleteDialog>

            <DeleteDialog
                title="Delete Document"
                url={deletingDocument
                    ? route('folders.documents.destroy', {folder: deletingDocument.folder_id, document: deletingDocument})
                    : '#'
                }
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

Index.layout = page => <TenantLayout children={page} />

export default Index;