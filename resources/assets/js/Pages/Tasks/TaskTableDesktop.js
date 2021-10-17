import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";

const MailingListTableDesktop = ({ tasks }) => (
    <Table
        headings={['Title', 'Role', 'Type', 'Created']}
        body={tasks.map((task) => (
            <tr key={task.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div className="ml-4">
                            <Link href={route('tasks.show', task.id)} className="text-sm font-medium text-purple-800">{task.name}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    {task.role.name}
                </TableCell>
                <TableCell>
                    {task.type[0].toUpperCase() + task.type.slice(1)}
                    {task.type === 'form' && <span className="text-xs ml-1.5">({task.route})</span>}
                </TableCell>
                <TableCell>
                    <DateTag date={task.created_at} />
                </TableCell>
            </tr>
        ))}
    />
);

export default MailingListTableDesktop;