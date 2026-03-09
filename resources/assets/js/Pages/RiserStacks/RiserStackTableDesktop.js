import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import Badge from "../../components/Badge";

const RiserStackTableDesktop = ({ stacks, userEnsemblesCount }) => {
    const { route } = useRoute();

    const headings = collect({
        title: 'Title',
        ensembles: userEnsemblesCount > 1 ? 'Ensembles' : null,
        created: 'Created',
    }).filter(h => h !== null);

    return (
        <Table
            pagination={<Pagination details={stacks} />}
            headings={headings}
            body={stacks.data.map((stack) => (
                <tr key={stack.id}>
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
                </tr>
            ))}
        />
    );
}

export default RiserStackTableDesktop;