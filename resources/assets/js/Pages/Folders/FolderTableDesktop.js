import React, {useState} from 'react';
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import classNames from "../../classNames";
import FolderIcon from "../../components/FolderIcon";

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
                                        className="text-purple-800"
                                        onClick={() => setOpenFolder(folder.id === openFolder ? 0 : folder.id)}
                                    >
                                        <i className={classNames(
                                            'fas fa-fw mr-1.5',
                                            folder.id === openFolder ? 'fa-folder-open' : 'fa-folder'
                                        )}/>
                                        {folder.title}
                                    </a>
                                </div>
                            </div>
                        </TableCell>
                        <TableCell>
                            {DateTime.fromJSDate(new Date(folder.created_at)).toLocaleString(DateTime.DATE_MED)}
                        </TableCell>
                    </tr>
                    {folder.id === openFolder && folder.documents.map((document) => (
                        <tr key={document.id}>
                            <TableCell>
                                <div className="flex items-center">
                                    <div className="ml-4">
                                        <a href={document.download_url} download={document.title} className="text-purple-800">
                                            <i className="fas fa-fw fa-level-up-alt fa-rotate-90 mr-1.5" />
                                            <FolderIcon icon={document.icon} />
                                            {document.title}
                                        </a>
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell>
                                {DateTime.fromJSDate(new Date(document.created_at)).toLocaleString(DateTime.DATE_MED)}
                            </TableCell>
                        </tr>
                    ))}
                </React.Fragment>
            ))}
        />
    );
}

export default FolderTableDesktop;