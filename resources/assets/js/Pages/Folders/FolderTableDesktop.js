import React, {useEffect, useState} from 'react';
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import DocumentForm from "./DocumentForm";
import {useForm} from "@inertiajs/react";
import Dialog from "../../components/Dialog";
import Form from "../../components/Form";
import Label from "../../components/inputs/Label";
import Error from "../../components/inputs/Error";
import TextInput from "../../components/inputs/TextInput";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";
import TableHeadingSort from "../../components/TableHeadingSort";
import {DocumentRowDesktop, FolderRowDesktop} from "./FolderTableRows";

const FolderTableDesktop = ({ folders, documents, isFiltered, setDeletingFolder, setDeletingDocument, permissions, userEnsemblesCount, sortFilterForm }) => {
    const [openFolder, setOpenFolder] = useState(0);
    const [renameDocumentIsOpen, setRenameDocumentIsOpen] = useState(false);
    const [renamingDocument, setRenamingDocument] = useState({ folder: folders[0], document: folders[0]?.documents?.[0] });

    const showEnsemblesColumn = userEnsemblesCount > 1;

    return (
        <>
            <Table>
                <THead>
                    <tr>
                        <TableHeading>
                            <TableHeadingSort form={sortFilterForm} sort="title">
                                Title
                            </TableHeadingSort>
                        </TableHeading>
                        {showEnsemblesColumn && <TableHeading>Ensembles</TableHeading>}
                        <TableHeading>
                            <TableHeadingSort form={sortFilterForm} sort="created_at">
                                Created
                            </TableHeadingSort>
                        </TableHeading>
                        {(permissions['delete_folder'] || permissions['delete_document']) && (
                            <TableHeading>Delete</TableHeading>
                        )}
                    </tr>
                </THead>
                <TBody>
                    {isFiltered && documents.length > 0 && (
                        <>
                            <tr className="bg-gray-50">
                                <TableCell colSpan={showEnsemblesColumn ? 4 : 3} className="py-2 px-4 font-semibold text-gray-700">
                                    Matching Documents
                                </TableCell>
                            </tr>
                            {documents.map((document) => (
                                <DocumentRowDesktop
                                    key={`doc-${document.id}`}
                                    document={document}
                                    folder={{id: document.folder_id}}
                                    permissions={permissions}
                                    setRenamingDocument={setRenamingDocument}
                                    setRenameDocumentIsOpen={setRenameDocumentIsOpen}
                                    setDeletingDocument={setDeletingDocument}
                                    showEnsemblesColumn={showEnsemblesColumn}
                                />
                            ))}
                            <tr className="bg-gray-50">
                                <TableCell colSpan={showEnsemblesColumn ? 4 : 3} className="py-2 px-4 font-semibold text-gray-700">
                                    Matching Folders
                                </TableCell>
                            </tr>
                        </>
                    )}
                    {folders.map((folder) => (
                        <React.Fragment key={folder.id}>
                            <FolderRowDesktop
                                folder={folder}
                                isOpen={folder.id === openFolder}
                                toggleOpen={() => setOpenFolder(folder.id === openFolder ? 0 : folder.id)}
                                showEnsemblesColumn={showEnsemblesColumn}
                                permissions={permissions}
                                setDeletingFolder={setDeletingFolder}
                            />
                            {folder.id === openFolder && <>
                                {folder.documents.map((document) => (
                                    <DocumentRowDesktop
                                        key={document.id}
                                        document={document}
                                        folder={folder}
                                        permissions={permissions}
                                        setRenamingDocument={setRenamingDocument}
                                        setRenameDocumentIsOpen={setRenameDocumentIsOpen}
                                        setDeletingDocument={setDeletingDocument}
                                        showEnsemblesColumn={showEnsemblesColumn}
                                        isInsideFolder
                                    />
                                ))}
                                {folder.documents.length === 0 && (
                                    <tr>
                                        <TableCell colSpan={showEnsemblesColumn ? 4 : 3}>
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
                                    <TableCell colSpan={showEnsemblesColumn ? 4 : 3}>
                                        <DocumentForm folder={folder} />
                                    </TableCell>
                                </tr>
                                )}
                            </>}
                        </React.Fragment>
                    ))}
                </TBody>
            </Table>
            <RenameDocumentDialog folder={renamingDocument.folder} document={renamingDocument.document} isOpen={renameDocumentIsOpen} setIsOpen={setRenameDocumentIsOpen} />
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