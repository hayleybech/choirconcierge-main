import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import Icon from "../../components/Icon";
import SectionTitle from "../../components/SectionTitle";
import DateTag from "../../components/DateTag";
import DeleteDialog from "../../components/DeleteDialog";
import EmptyState from "../../components/EmptyState";

const Show = ({ list }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${list.title} - Mailing Lists`} />
            <PageHeader
                title={list.title}
                meta={[
                    <>
                        <Icon icon={list.type_icon} mr className="text-gray-400" />
                        {list.list_type.charAt(0).toUpperCase() + list.list_type.slice(1)}
                    </>,
                    <><strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500">{ list.email.split('@')[1] }</span></>,
                    <DateTag date={list.created_at} label="Created" />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Mailing Lists', url: route('groups.index')},
                    { name: list.title, url: route('groups.show', list) },
                ]}
                actions={[
                    { label: 'Edit', icon: 'edit', url: route('groups.edit', list), can: 'update_group' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_group' },
                ].filter(action => action.can ? list.can[action.can] : true)}
            />

            <DeleteDialog title="Delete Mailing Lists" url={route('groups.destroy', list)} isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen}>
                Are you sure you want to delete this mailing list? This action cannot be undone.
            </DeleteDialog>

            <div className="grid grid-cols-1 sm:grid-cols-2 h-full sm:divide-x sm:divide-x-gray-300">

                <div className="sm:col-span-1">
                    <div className="py-6 px-4 sm:px-6 lg:px-8">
                        <SectionTitle>Recipients</SectionTitle>
                    </div>
                    <div className="h-full overflow-y-auto relative">
                        {list.members.length > 0
                            ? <>
                                <div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                                    <h3>Recipients</h3>
                                </div>
                                <ul role="list" className="relative z-0 divide-y divide-gray-200">
                                    {list.members.map((member) => (
                                        <li key={member.id} >
                                            <div className="relative px-6 py-5 flex items-center space-x-3">
                                                <div className="flex items-center justify-between px-4">
                                                    <span className="text-sm font-medium truncate">
                                                        {`${getTypeName(member.memberable_type)}: `}
                                                        {member?.memberable.name ?? member?.memberable.title ?? 'Recipient not found'}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </>
                            : <EmptyState
                                title="No recipients"
                                description="This mailing list has no recipients, so it won't be able to do very much. "
                                actionDescription={list.can['update_group']
                                    ? 'To add recipients, edit this list, then assign a singer or an entire role, category or voice part.'
                                    : 'Ask your admin to finish setting up this list.'
                                }
                                icon="inbox-in"
                                href={list.can['update_group'] ? route('groups.edit', list) : null}
                                actionLabel="Edit Mailing List"
                                actionIcon="edit"
                            />
                        }
                    </div>
                </div>

                {list.list_type === 'distribution' && (
                <div className="sm:col-span-1">
                    <div className="py-6 px-4 sm:px-6 lg:px-8">
                        <SectionTitle>Senders</SectionTitle>
                    </div>
                    <div className="h-full overflow-y-auto relative">
                        {list.senders.length > 0
                            ? <>
                                <div
                                    className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                                    <h3>Senders</h3>
                                </div>
                                <ul role="list" className="relative z-0 divide-y divide-gray-200">
                                    {list.senders.map((sender) => (
                                        <li key={sender.id}>
                                            <div className="relative px-6 py-5 flex items-center space-x-3">
                                                <div className="flex items-center justify-between px-4">
                                                    <span className="text-sm font-medium truncate">
                                                        {`${getTypeName(sender.sender_type)}: `}
                                                        {sender?.sender.name ?? sender?.sender.title ?? 'Sender not found'}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </>
                            : <EmptyState
                                title="No senders"
                                description="This type of mailing list needs permitted senders to be assigned (a Chat type list just assumes the recipients are the senders). "
                                actionDescription={list.can['update_group']
                                    ? 'To add senders, edit this list, then assign a singer or an entire role, category or voice part.'
                                    : 'Ask your admin to finish setting up this list.'
                                }
                                icon="inbox-out"
                                href={list.can['update_group'] ? route('groups.edit', list) : null}
                                actionLabel="Edit Mailing List"
                                actionIcon="edit"
                            />
                        }
                    </div>
                </div>
                )}

            </div>
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;

const getTypeName = (type) => {
    const typeNames = {
        'App\\\Models\\Role': 'Role',
        'App\\Models\\VoicePart': 'Voice Part',
        'App\\Models\\SingerCategory': 'Singer Category',
        'App\\Models\\User': 'Singer',
    };
    return typeNames[type];
};