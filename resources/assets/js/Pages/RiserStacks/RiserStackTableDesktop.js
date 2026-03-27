import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading, TableSelectAll, TableCellSelect, TItemRow} from "../../components/Table";
import DateTag from "../../components/DateTag";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import Badge from "../../components/Badge";

const RiserStackTableDesktop = ({ stacks, userEnsemblesCount, bulkEdit }) => {
    const { route } = useRoute();

    return (
        <Table pagination={<Pagination details={stacks} />}>
            <THead>
                <tr>
                    <TableSelectAll bulkEdit={bulkEdit} totalItems={stacks.data.length} />
                    <TableHeading>Title</TableHeading>
                    {userEnsemblesCount > 1 && <TableHeading>Ensembles</TableHeading>}
                    <TableHeading>Created</TableHeading>
                </tr>
            </THead>
            <TBody>
                {stacks.data.map((stack) => (
                    <TItemRow key={stack.id} bulkEdit={bulkEdit} value={stack.id}>
                        <TableCellSelect bulkEdit={bulkEdit} value={stack.id} />
                        <TableCell>
                            <Link href={route('stacks.show', {stack: stack.id})} className="text-sm font-medium text-purple-800">
                                {stack.title}
                            </Link>
                        </TableCell>
                        {userEnsemblesCount > 1 && (
                            <TableCell>
                                <div className="flex flex-wrap gap-1">
                                    {stack.ensembles.map(ensemble => (
                                        <Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">
                                            {ensemble.name}
                                        </Badge>
                                    ))}
                                </div>
                            </TableCell>
                        )}
                        <TableCell>
                            <DateTag icon="pencil" date={stack.created_at} />
                        </TableCell>
                    </TItemRow>
                ))}
            </TBody>
        </Table>
    );
}

export default RiserStackTableDesktop;