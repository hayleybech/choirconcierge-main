import React from 'react';
import TableMobile, {
    TableMobileHeader,
    TableMobileListItem,
    TableMobileSelect,
    TableMobileSelectableLink
} from "../../components/TableMobile";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const MailingListTableMobile = ({ lists, bulkEdit }) => {
    const { route } = useRoute();

    const list_type_labels = {
        chat: 'Chat',
        public: 'Public',
        distribution: 'Mailout',
    }

    return (
        <div>
            <TableMobileHeader bulkEdit={bulkEdit} />
            <TableMobile pagination={<Pagination details={lists} />}>
                {lists.data.map((list) => (
                    <TableMobileListItem key={list.id}>
                        <TableMobileSelect bulkEdit={bulkEdit} value={list.id} />
                        <TableMobileSelectableLink
                            bulkEdit={bulkEdit}
                            value={list.id}
                            url={route('groups.show', {group: list.id})}
                        >
                            <div className="min-w-0 flex-1 pr-2 lg:grid lg:grid-cols-2 lg:gap-4">
                                <div className="flex items-center justify-between">
                                    <p className="flex items-center min-w-0 mr-1.5">
                                        <span className="text-sm font-medium text-purple-600 truncate">{list.title}</span>
                                    </p>
                                    <div className="text-xs text-gray-500 shrink-0">
                                        <Icon icon={list.type_icon} mr className="text-gray-400" />
                                        {list_type_labels[list.list_type]}
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                                        <Icon icon="envelope" mr className="text-gray-400" />
                                        <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500 hidden sm:flex">{ list.email.split('@')[1] }</span>
                                    </p>
                                </div>
                            </div>
                        </TableMobileSelectableLink>
                    </TableMobileListItem>
                ))}
            </TableMobile>
        </div>
    );
}

export default MailingListTableMobile;