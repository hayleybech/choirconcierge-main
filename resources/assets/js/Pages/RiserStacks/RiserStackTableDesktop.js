import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";

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
                    {DateTime.fromJSDate(new Date(stack.created_at)).toLocaleString(DateTime.DATE_MED)}
                </TableCell>
            </tr>
        ))}
    />
);

export default RiserStackTableDesktop;