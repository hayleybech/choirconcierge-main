import React from 'react';
import { TableCell } from "../../components/Table";
import FolderIcon from "../../components/FolderIcon";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import Button from "../../components/inputs/Button";
import Badge from "../../components/Badge";
import useRoute from "../../hooks/useRoute";

export const DocumentRowDesktop = ({ document, folder, permissions, setRenamingDocument, setRenameDocumentIsOpen, setDeletingDocument, showEnsemblesColumn, isInsideFolder = false }) => {
    return (
        <tr key={document.id}>
            <TableCell>
                <a href={document.download_url} download={document.title} target="_blank" className="text-purple-600">
                    {isInsideFolder && <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" />}
                    <FolderIcon icon={document.icon} />
                    {document.title}
                </a>
            </TableCell>
            {showEnsemblesColumn && <TableCell />}
            <TableCell>
                <DateTag icon="pencil" date={document.created_at} />
            </TableCell>
            <TableCell>
                <div className="flex items-center gap-2">
                    {permissions['update_document'] && (
                        <Button
                            variant="secondary"
                            size="xs"
                            className="ml-2"
                            onClick={(e) => { e.preventDefault(); setRenamingDocument({folder, document}); setRenameDocumentIsOpen(true); }}
                        >
                            <Icon icon="edit" />
                            Rename
                        </Button>
                    )}
                    {permissions['delete_document'] && (
                        <Button onClick={(e) => { e.preventDefault(); setDeletingDocument(document); }} variant="danger-outline" size="xs">
                            <Icon icon="times" /> Delete
                        </Button>
                    )}
                </div>
            </TableCell>
        </tr>
    );
};

export const FolderRowDesktop = ({ folder, isOpen, toggleOpen, showEnsemblesColumn, permissions, setDeletingFolder }) => {
    const { route } = useRoute();
    return (
        <tr key={folder.id}>
            <TableCell>
                <a
                    href="#"
                    className="text-purple-600"
                    onClick={(e) => { e.preventDefault(); toggleOpen(); }}
                >
                    <Icon icon={isOpen ? 'folder-open' : 'folder'} mr className="text-purple-500" />
                    {folder.title}
                </a>
            </TableCell>
            {showEnsemblesColumn && (
                <TableCell>
                    <div className="space-x-1.5 space-y-1.5">
                        {folder.ensembles.map(ensemble => (
                            <Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">{ensemble.name}</Badge>
                        ))}
                    </div>
                </TableCell>
            )}
            <TableCell>
                <DateTag icon="pencil" date={folder.created_at} />
            </TableCell>
            <TableCell>
                <div className="flex items-center gap-2">
                    {permissions['update_folder'] && (
                        <Button
                            href={route('folders.edit', { folder })}
                            variant="secondary"
                            size="xs"
                            className="ml-2"
                        >
                            <Icon icon="edit" />
                            Edit
                        </Button>
                    )}
                    {permissions['delete_folder'] && (
                        <Button onClick={(e) => { e.preventDefault(); setDeletingFolder(folder); }} variant="danger-outline" size="xs">
                            <Icon icon="times" /> Delete
                        </Button>
                    )}
                </div>
            </TableCell>
        </tr>
    );
};

export const DocumentRowMobile = ({ document, permissions, setDeletingDocument, isInsideFolder = false }) => {
    return (
        <li key={document.id}>
            <a href={document.download_url} download={document.title} target="_blank" className={`block hover:bg-gray-50 ${!isInsideFolder ? 'border-b border-gray-200' : ''}`}>
                <div className="flex items-center py-4 sm:px-6">
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center min-w-0 mr-1.5">
                                {isInsideFolder && <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" mr />}
                                <FolderIcon icon={document.icon} />
                                <span className="text-sm font-medium text-purple-600 truncate">{document.title}</span>
                            </div>

                            {permissions['delete_document'] && (
                                <Button onClick={(e) => { e.preventDefault(); e.stopPropagation(); setDeletingDocument(document); }} variant="danger-outline" size="sm">
                                    <Icon icon="trash" />
                                </Button>
                            )}
                        </div>
                    </div>
                </div>
            </a>
        </li>
    );
};

export const FolderRowMobile = ({ folder, isOpen, toggleOpen, showEnsemblesColumn, permissions, setDeletingFolder }) => {
    const { route } = useRoute();
    return (
        <li key={folder.id}>
            <a href="#" onClick={(e) => { e.preventDefault(); toggleOpen(); }} className="block hover:bg-gray-50">
                <div className="flex items-center py-4 sm:px-6">
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center min-w-0 mr-1.5">
                                <Icon icon={isOpen ? 'folder-open' : 'folder'} mr className="text-purple-500" />
                                <span className="text-sm font-medium text-purple-600 truncate">{folder.title}</span>
                            </div>

                            <div className="flex gap-2">
                                {permissions['update_folder'] && (
                                    <Button href={route('folders.edit', { folder })} variant="secondary" size="sm">
                                        <Icon icon="edit" />
                                    </Button>
                                )}
                                {permissions['delete_folder'] && (
                                    <Button onClick={(e) => { e.preventDefault(); e.stopPropagation(); setDeletingFolder(folder); }} variant="danger-outline" size="sm">
                                        <Icon icon="trash" />
                                    </Button>
                                )}
                            </div>
                        </div>
                        {showEnsemblesColumn && folder.ensembles.length > 0 && (
                            <div className="mt-2 flex flex-wrap gap-1">
                                {folder.ensembles.map(ensemble => (
                                    <Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">{ensemble.name}</Badge>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </a>
        </li>
    );
};
