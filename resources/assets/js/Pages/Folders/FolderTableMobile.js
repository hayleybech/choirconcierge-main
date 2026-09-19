import React, {useState} from 'react';
import TableMobile, { TableMobileHeader } from "../../components/TableMobile";
import Icon from "../../components/Icon";
import DocumentForm from "./DocumentForm";
import Button from "../../components/inputs/Button";
import EmptyState from "../../components/EmptyState";
import {DocumentRowMobile, FolderRowMobile} from "./FolderTableRows";

const FolderTableMobile = ({ folders, documents, isFiltered, setShowFilters, setDeletingFolder, setDeletingDocument, permissions, userEnsemblesCount }) => {
    const [openFolder, setOpenFolder] = useState(0);

    const showEnsemblesColumn = userEnsemblesCount > 1;

    const bulkEdit = {
        isActiveMobile: false,
        noun: 'Folder',
        selectedIds: [],
        totalItems: folders.length + (documents ? documents.length : 0),
    };

    return (
        <div>
            <TableMobileHeader bulkEdit={bulkEdit}>
				<Button
					variant={isFiltered ? 'success-outline' : 'clear-v2'}
					size="xs"
					onClick={() => setShowFilters(prev => !prev)}
				>
					<Icon icon="filter" mr />
					Filter/Sort
				</Button>
			</TableMobileHeader>
            <TableMobile>
            {isFiltered && documents.length > 0 && (
                <>
                    <li className="bg-gray-50 px-4 py-2 font-semibold text-gray-700 text-sm">
                        Matching Documents
                    </li>
                    {documents.map((document) => (
                        <DocumentRowMobile
                            key={`doc-${document.id}`}
                            document={document}
                            permissions={permissions}
                            setDeletingDocument={setDeletingDocument}
                        />
                    ))}
                    <li className="bg-gray-50 px-4 py-2 font-semibold text-gray-700 text-sm">
                        Folders
                    </li>
                </>
            )}
            {folders.map((folder) => (
                <React.Fragment key={folder.id}>
                    <FolderRowMobile
                        folder={folder}
                        isOpen={folder.id === openFolder}
                        toggleOpen={() => setOpenFolder(folder.id === openFolder ? 0 : folder.id)}
                        showEnsemblesColumn={showEnsemblesColumn}
                        permissions={permissions}
                        setDeletingFolder={setDeletingFolder}
                    />
                    {folder.id === openFolder && (
                        <TableMobile>
                            {folder.documents.map((document) => (
                                <DocumentRowMobile
                                    key={document.id}
                                    document={document}
                                    permissions={permissions}
                                    setDeletingDocument={setDeletingDocument}
                                    isInsideFolder
                                />
                            ))}
                            {folder.documents.length === 0 && (
                                <EmptyState
                                    title="No documents"
                                    description="This folder is empty. "
                                    actionDescription={permissions['create_document'] ? 'Use the form below to add a document.' : null}
                                    icon="file"
                                />
                            )}
                            {permissions['create_document'] && (
                                <div className="flex items-center px-4 py-4 sm:px-6 ml">
                                    <div className="px-4 w-full">
                                        <DocumentForm folder={folder} />
                                    </div>
                                </div>
                            )}
                        </TableMobile>
                    )}
                </React.Fragment>
            ))}
            </TableMobile>
        </div>
    );
}

export default FolderTableMobile;