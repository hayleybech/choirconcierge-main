import React from 'react';
import TableMobile, {TableMobileHeader, TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";
import { plural } from '../../util';

const RoleTableMobile = ({ roles }) => {
    const { route } = useRoute();
    const bulkEdit = {
        isActiveMobile: false,
        noun: 'Role',
        selectedIds: [],
        totalItems: roles.length,
    };

    return (
        <div>
            <TableMobileHeader bulkEdit={bulkEdit} />
            <TableMobile>
            {roles.map((role) => (
                <TableMobileItem key={role.id} url={route('roles.show', {role: role.id})}>
                    <p className="flex items-center min-w-0 mr-1.5">
                        <span className="text-sm font-medium text-purple-600 truncate">{role.name}</span>
                    </p>
                    <p className="flex items-center min-w-0 mr-1.5">
                        <span className="text-sm font-medium text-gray-500">
                            {role.singers_count} {plural('singer', role.singers_count)}
                        </span>
                    </p>
                </TableMobileItem>
            ))}
            </TableMobile>
        </div>
    );
}

export default RoleTableMobile;