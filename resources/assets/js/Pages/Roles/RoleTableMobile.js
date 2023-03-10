import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import useRoute from "../../hooks/useRoute";

const RoleTableMobile = ({ roles }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {roles.map((role) => (
                <TableMobileItem key={role.id} url={route('roles.show', {role: role.id})}>
                    <p className="flex items-center min-w-0 mr-1.5">
                        <span className="text-sm font-medium text-purple-600 truncate">{role.name}</span>
                    </p>
                    <p className="flex items-center min-w-0 mr-1.5">
                    <span className="text-sm font-medium text-sm text-gray-500">
                        {role.singers_count} {role.singers_count === 1 ? 'singer' : 'singers'}
                    </span>
                    </p>
                </TableMobileItem>
            ))}
        </TableMobile>
    );
}

export default RoleTableMobile;