import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Table, {TableCell} from "../../components/Table";
import collect from "collect.js";

const RoleTableDesktop = ({ roles }) => {
    const headings = collect({
        name: 'Name',
        singers: 'Singers',
    })

    return (
        <Table
            headings={headings}
            body={roles.map((role) => (
                <tr key={role.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="ml-4">
                                <Link href={route('roles.show', role.id)} className="text-sm font-medium text-purple-800">{role.name}</Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        {role.singers_count}
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default RoleTableDesktop;