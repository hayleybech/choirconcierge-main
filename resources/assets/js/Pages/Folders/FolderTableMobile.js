import React, {useState} from 'react';
import TableMobile from "../../components/TableMobile";
import FolderIcon from "../../components/FolderIcon";
import Icon from "../../components/Icon";
import DocumentForm from "./DocumentForm";
import Button from "../../components/inputs/Button";
import EmptyState from "../../components/EmptyState";

const FolderTableMobile = ({ folders, setDeletingFolder, setDeletingDocument, permissions }) => {
    const [openFolder, setOpenFolder] = useState(0);

    return (
        <TableMobile>
            {folders.map((folder) => (
                <li key={folder.id}>
                    <a href="#" onClick={() => setOpenFolder(folder.id === openFolder ? 0 : folder.id)} className="block hover:bg-gray-50">
                        <div className="flex items-center px-4 py-4 sm:px-6">
                            <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div className="flex items-center justify-between">
                                    <p className="flex items-center min-w-0 mr-1.5">
                                        <Icon icon={folder.id === openFolder ? 'folder-open' : 'folder'} mr className="text-purple-500" />
                                        <span className="text-sm font-medium text-purple-600 truncate">{folder.title}</span>
                                    </p>

                                    {permissions['folders_delete'] && (
                                        <Button onClick={() => setDeletingFolder(folder)} variant="danger-outline" size="sm">
                                            <Icon icon="trash" />
                                        </Button>
                                    )}
                                </div>
                            </div>
                        </div>
                    </a>
                    {folder.id === openFolder && (
                        <TableMobile>
                            {folder.documents.map((document) => (
                                <a href={document.download_url} download={document.title} key={document.id} className="block hover:bg-gray-50">
                                    <div className="flex items-center px-4 py-4 sm:px-6">
                                        <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                            <div className="flex items-center justify-between">
                                                <p className="flex items-center min-w-0 mr-1.5">
                                                    <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" mr />
                                                    <FolderIcon icon={document.icon} />
                                                    <span className="text-sm font-medium text-purple-600 truncate">{document.title}</span>
                                                </p>

                                                {permissions['documents_delete'] && (
                                                    <Button onClick={() => setDeletingDocument(document)} variant="danger-outline" size="sm">
                                                        <Icon icon="trash" />
                                                    </Button>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </a>
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
                </li>
            ))}
        </TableMobile>
    );
}

export default FolderTableMobile;