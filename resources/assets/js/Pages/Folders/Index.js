import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import FolderTableDesktop from "./FolderTableDesktop";
import FolderTableMobile from "./FolderTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import DeleteDialog from "../../components/DeleteDialog";
import MailingListTableDesktop from "../MailingLists/MailingListTableDesktop";
import MailingListTableMobile from "../MailingLists/MailingListTableMobile";
import EmptyState from "../../components/EmptyState";
import IndexContainer from "../../components/IndexContainer";

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

            <IndexContainer
                tableDesktop={<FolderTableDesktop folders={folders} setDeletingFolder={setDeletingFolder} setDeletingDocument={setDeletingDocument} permissions={can} />}
                tableMobile={<FolderTableMobile folders={folders} setDeletingFolder={setDeletingFolder} setDeletingDocument={setDeletingDocument} permissions={can} />}
                emptyState={folders.length === 0
                    ? <EmptyState
                        title="No folders"
                        description={<>
                            Looks like you haven't made any folders or documents yet. This is a great place to store meeting minutes, your constitution, or other important files.<br />
                            Get started by adding a folder, then upload some documents to the folder.
                        </>}
                        icon="folders"
                        href={route('folders.create')}
                        actionLabel="Add Folder"
                        actionIcon="folder-plus"
                    />
                    : null
                }
            />

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