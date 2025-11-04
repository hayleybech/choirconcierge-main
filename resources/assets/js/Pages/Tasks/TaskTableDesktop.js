import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const TaskTableDesktop = ({ tasks }) => {
    const { route } = useRoute();

    const headings = collect({
        title: 'Title',
        role: 'Role',
        type: 'Type',
        created: 'Created',
    });

    return (
        <Table
            pagination={<Pagination details={tasks} />}
            headings={headings}
            body={tasks.data.map((task) => (
                <tr key={task.id}>
                    <TableCell>
                        <Link href={route('tasks.show', {task: task.id})} className="text-sm font-medium text-purple-800">{task.name}</Link>
                    </TableCell>
                    <TableCell>
                        {task.role?.name}
                    </TableCell>
                    <TableCell>
                        {task.type[0].toUpperCase() + task.type.slice(1)}
                        {task.type === 'form' && <span className="text-xs ml-1.5">({task.route})</span>}
                    </TableCell>
                    <TableCell>
                        <DateTag icon="pencil" date={task.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default TaskTableDesktop;