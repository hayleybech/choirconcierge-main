import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading, TableSelectAll, TableCellSelect, TItemRow} from "../../components/Table";
import DateTag from "../../components/DateTag";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import EnsembleBadges from "../../components/EnsembleBadges";
import TableHeadingSort from '../../components/TableHeadingSort';

const RiserStackTableDesktop = ({ stacks, userEnsemblesCount, bulkEdit, sortFilterForm }) => {
    const { route } = useRoute();

    return (
        <Table pagination={<Pagination details={stacks} />}>
            <THead>
                <tr>
                    <TableSelectAll bulkEdit={bulkEdit} totalItems={stacks.data.length} />
                    <TableHeading><TableHeadingSort form={sortFilterForm} sort="title">Name</TableHeadingSort></TableHeading>
                    {userEnsemblesCount > 1 && <TableHeading>Ensembles</TableHeading>}
                    <TableHeading><TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort></TableHeading>
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
                                <EnsembleBadges ensembles={stack.ensembles} />
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