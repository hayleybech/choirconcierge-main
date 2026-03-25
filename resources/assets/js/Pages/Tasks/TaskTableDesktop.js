import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import DateTag from "../../components/DateTag";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const TaskTableDesktop = ({ tasks }) => {
    const { route } = useRoute();

    return (
        <Table pagination={<Pagination details={tasks} />}>
            <THead>
                <tr>
                    <TableHeading>Title</TableHeading>
                    <TableHeading>Role</TableHeading>
                    <TableHeading>Type</TableHeading>
                    <TableHeading>Created</TableHeading>
                </tr>
            </THead>
            <TBody>
                {tasks.data.map((task) => (
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
            </TBody>
        </Table>
    );
}

export default TaskTableDesktop;