import React, {useEffect, useState} from 'react';
import Table, {TableCell} from "../../components/Table";
import FolderIcon from "../../components/FolderIcon";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import DocumentForm from "./DocumentForm";
import Button from "../../components/inputs/Button";
import {useForm} from "@inertiajs/react";
import Dialog from "../../components/Dialog";
import Form from "../../components/Form";
import Label from "../../components/inputs/Label";
import Error from "../../components/inputs/Error";
import TextInput from "../../components/inputs/TextInput";
import collect from "collect.js";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";

const FolderTableDesktop = ({ folders, setDeletingFolder, setDeletingDocument, permissions }) => {
    const [openFolder, setOpenFolder] = useState(0);
    const [renameDocumentIsOpen, setRenameDocumentIsOpen] = useState(false);
    const [renamingDocument, setRenamingDocument] = useState({ folder: folders[0], document: folders[0].documents[0] });
    const [renameFolderIsOpen, setRenameFolderIsOpen] = useState(false);
    const [renamingFolder, setRenamingFolder] = useState(folders[0]);

    const headings = collect({
        title: 'Title',
        created: 'Created',
        delete: 'Delete',
    }).filter((item, key) => key !== 'delete' ||  permissions['delete_folder'] || permissions['delete_document']);

    return (
        <>
            <Table
                headings={headings}
                body={folders.map((folder) => (
                    <React.Fragment key={folder.id}>
                        <tr>
                            <TableCell>
                                <div className="flex items-center">
                                    <div className="ml-4">
                                        <a
                                            href="#"
                                            className="text-purple-600"
                                            onClick={() => setOpenFolder(folder.id === openFolder ? 0 : folder.id)}
                                        >
                                            <Icon icon={folder.id === openFolder ? 'folder-open' : 'folder'} mr className="text-purple-500" />
                                            {folder.title}
                                        </a>
                                        {permissions['update_folder'] && (
                                        <Button
                                            variant="secondary"
                                            size="xs"
                                            className="ml-2"
                                            onClick={() => { setRenamingFolder(folder); setRenameFolderIsOpen(true); }}
                                        >
                                            <Icon icon="edit" />
                                            Rename
                                        </Button>
                                        )}
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <DateTag date={folder.created_at} />
                            </TableCell>
                            <TableCell>
                                {permissions['delete_folder'] && (
                                    <Button onClick={() => setDeletingFolder(folder)} variant="danger-outline">
                                        <Icon icon="times" />
                                    </Button>
                                )}
                            </TableCell>
                        </tr>
                        {folder.id === openFolder && <>
                            {folder.documents.map((document) => (
                                <tr key={document.id}>
                                    <TableCell>
                                        <div className="flex items-center">
                                            <div className="ml-4">
                                                <a href={document.download_url} download={document.title} target="_blank" className="text-purple-600">
                                                    <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" />
                                                    <FolderIcon icon={document.icon} />
                                                    {document.title}
                                                </a>
                                                {permissions['update_document'] && (
                                                <Button
                                                    variant="secondary"
                                                    size="xs"
                                                    className="ml-2"
                                                    onClick={() => { setRenamingDocument({folder, document}); setRenameDocumentIsOpen(true); }}
                                                >
                                                    <Icon icon="edit" />
                                                    Rename
                                                </Button>
                                                )}
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <DateTag date={document.created_at} />
                                    </TableCell>
                                    <TableCell>
                                        {permissions['delete_document'] && (
                                            <Button onClick={() => setDeletingDocument(document)} variant="danger-outline">
                                                <Icon icon="times" />
                                            </Button>
                                        )}
                                    </TableCell>
                                </tr>
                            ))}
                            {folder.documents.length === 0 && (
                                <tr>
                                    <TableCell colSpan={3}>
                                        <EmptyState
                                            title="No documents"
                                            description="This folder is empty. "
                                            actionDescription={permissions['create_document'] ? 'Use the form below to add a document.' : null}
                                            icon="file"
                                        />
                                    </TableCell>
                                </tr>
                            )}
                            {permissions['create_document'] && (
                            <tr>
                                <TableCell colSpan={3}>
                                    <div className="ml-4">
                                        <DocumentForm folder={folder} />
                                    </div>
                                </TableCell>
                            </tr>
                            )}
                        </>}
                    </React.Fragment>
                ))}
            />
            <RenameDocumentDialog folder={renamingDocument.folder} document={renamingDocument.document} isOpen={renameDocumentIsOpen} setIsOpen={setRenameDocumentIsOpen} />
            <RenameFolderDialog folder={renamingFolder} isOpen={renameFolderIsOpen} setIsOpen={setRenameFolderIsOpen} />
        </>
    );
}

export default FolderTableDesktop;

const RenameDocumentDialog = ({ isOpen, setIsOpen, folder, document }) => {
    const { route } = useRoute();

    const { data, setData, put, errors } = useForm({
        title: document?.title ?? '',
    });

    useEffect(() => {
        setData('title', document?.title ?? '');
    }, [document]);

    function submit(e) {
        e.preventDefault();
        put(route('folders.documents.update', {folder, document}), {
            onSuccess: () => setIsOpen(false),
        });
    }

    return (
        <Dialog
            title="Rename document"
            okLabel="Rename"
            onOk={submit}
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <Form onSubmit={submit}>
                <div className="sm:col-span-6">
                    <Label label="New name" forInput="title" />
                    <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                    {errors.title && <Error>{errors.title}</Error>}
                </div>
            </Form>
        </Dialog>
    )
};

const RenameFolderDialog = ({ isOpen, setIsOpen, folder }) => {
    const { route } = useRoute();

    const { data, setData, put, errors } = useForm({
        title: folder?.title ?? '',
    });

    useEffect(() => {
        setData('title', folder?.title ?? '');
    }, [folder]);

    function submit(e) {
        e.preventDefault();
        put(route('folders.update', {folder}), {
            onSuccess: () => setIsOpen(false),
        });
    }

    return (
        <Dialog
            title="Rename folder"
            okLabel="Rename"
            onOk={submit}
            okVariant="primary"
            isOpen={isOpen}
            setIsOpen={setIsOpen}
        >
            <Form onSubmit={submit}>
                <div className="sm:col-span-6">
                    <Label label="New name" forInput="title" />
                    <TextInput name="title" value={data.title} updateFn={value => setData('title', value)} hasErrors={ !! errors['title'] } />
                    {errors.title && <Error>{errors.title}</Error>}
                </div>
            </Form>
        </Dialog>
    )
};