import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const RiserStackTableDesktop = ({ stacks }) => {
    const { route } = useRoute();

    const headings = collect({
        title: 'Title',
        created: 'Created',
    })

    return (
        <Table
            pagination={<Pagination details={stacks} />}
            headings={headings}
            body={stacks.data.map((stack) => (
                <tr key={stack.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="ml-4">
                                <Link href={route('stacks.show', {stack: stack.id})} className="text-sm font-medium text-purple-800">
                                    {stack.title}
                                </Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <DateTag icon="pencil" date={stack.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default RiserStackTableDesktop;