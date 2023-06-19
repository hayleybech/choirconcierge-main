import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";

const MailingListTableMobile = ({ lists }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {lists.map((list) => (
                <TableMobileItem key={list.id} url={route('groups.show', {group: list.id})}>
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div className="flex items-center justify-between">
                            <p className="flex items-center min-w-0 mr-1.5">
                                <span className="text-sm font-medium text-purple-600 truncate">{list.title}</span>
                            </p>
                            <div className="text-xs text-gray-500 shrink-0">
                                <Icon icon={list.type_icon} mr className="text-gray-400" />
                                {list.list_type.charAt(0).toUpperCase() + list.list_type.slice(1)}
                            </div>
                        </div>
                        <div className="flex items-center justify-between">
                            <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                                <Icon icon="envelope" mr className="text-gray-400" />
                                <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500 hidden sm:flex">{ list.email.split('@')[1] }</span>
                            </p>
                        </div>
                    </div>
                </TableMobileItem>
            ))}
        </TableMobile>
    );
}

export default MailingListTableMobile;