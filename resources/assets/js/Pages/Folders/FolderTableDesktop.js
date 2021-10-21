import React, {useState} from 'react';
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import FolderIcon from "../../components/FolderIcon";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";

const FolderTableDesktop = ({ folders }) => {
    const [openFolder, setOpenFolder] = useState(0);

    return (
        <Table
            headings={['Title', 'Created']}
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
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            <DateTag date={folder.created_at} />
                        </TableCell>
                    </tr>
                    {folder.id === openFolder && folder.documents.map((document) => (
                        <tr key={document.id}>
                            <TableCell>
                                <div className="flex items-center">
                                    <div className="ml-4">
                                        <a href={document.download_url} download={document.title} className="text-purple-600">
                                            <Icon icon="level-up-alt" className="fa-rotate-90 text-purple-500" />
                                            <FolderIcon icon={document.icon} />
                                            {document.title}
                                        </a>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                <DateTag date={document.created_at} />
                            </TableCell>
                        </tr>
                    ))}
                </React.Fragment>
            ))}
        />
    );
}

export default FolderTableDesktop;