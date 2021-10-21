import React, {useState} from 'react';
import TableMobile from "../../components/TableMobile";
import FolderIcon from "../../components/FolderIcon";
import Icon from "../../components/Icon";

const FolderTableMobile = ({ folders }) => {
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
                                </div>
                            </div>
                        </div>
                    </a>
                    <TableMobile>
                    {folder.id === openFolder && folder.documents.map((document) => (
                        <a href={document.download_url} download={document.title} key={document.id} className="block hover:bg-gray-50">
                            <div className="flex items-center px-4 py-4 sm:px-6">
                                <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                                    <div className="flex items-center justify-between">
                                        <p className="flex items-center min-w-0 mr-1.5">
                                            <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" mr />
                                            <FolderIcon icon={document.icon} />
                                            <span className="text-sm font-medium text-purple-600 truncate">{document.title}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    ))}
                    </TableMobile>
                </li>
            ))}
        </TableMobile>
    );
}

export default FolderTableMobile;