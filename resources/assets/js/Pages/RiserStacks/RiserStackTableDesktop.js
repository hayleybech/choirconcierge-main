import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Table, {TableCell} from "../../components/Table";
import DateTag from "../../components/DateTag";

const RiserStackTableDesktop = ({ stacks }) => (
    <Table
        headings={['Title', 'Created']}
        body={stacks.map((stack) => (
            <tr key={stack.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div className="ml-4">
                            <Link href={route('stacks.show', stack.id)} className="text-sm font-medium text-purple-800">{stack.title}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    <DateTag date={stack.created_at} />
                </TableCell>
            </tr>
        ))}
    />
);

export default RiserStackTableDesktop;