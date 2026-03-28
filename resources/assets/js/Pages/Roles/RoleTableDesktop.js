import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import useRoute from "../../hooks/useRoute";

const RoleTableDesktop = ({ roles }) => {
    const { route } = useRoute();

    return (
        <Table>
            <THead>
                <tr>
                    <TableHeading>Name</TableHeading>
                    <TableHeading>Singers</TableHeading>
                </tr>
            </THead>
            <TBody>
                {roles.map((role) => (
                    <tr key={role.id}>
                        <TableCell>
                            <Link href={route('roles.show', {role: role.id})} className="text-sm font-medium text-purple-800">
                                {role.name}
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Link href={route('singers.index')} data={{ filter: { 'roles.id': [role.id] } }} className="text-purple-800">
                                {role.singers_count} {role.singers_count === 1 ? 'singer' : 'singers'}
                            </Link>
                        </TableCell>
                    </tr>
                ))}
            </TBody>
        </Table>
    );
}

export default RoleTableDesktop;