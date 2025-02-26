import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import Icon from "../../components/Icon";
import SectionTitle from "../../components/SectionTitle";
import {Link} from "@inertiajs/react";
import ButtonLink from "../../components/inputs/ButtonLink";
import DateTag from "../../components/DateTag";
import SectionHeader from "../../components/SectionHeader";
import DeleteDialog from "../../components/DeleteDialog";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";

const Show = ({ task }) => {
    const { route } = useRoute();

    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${task.name} - Tasks`} />
            <PageHeader
                title={task.name}
                meta={[
                    <>{task.role.name}</>,
                    <>
                        {task.type[0].toUpperCase() + task.type.slice(1)}
                        {task.type === 'form' && <span className="text-xs ml-1.5">({task.route})</span>}
                    </>,
                    <DateTag date={task.created_at} label="Created" />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Tasks', url: route('tasks.index')},
                    { name: task.name, url: route('tasks.show', {task}) },
                ]}
                actions={[
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_task' },
                ].filter(action => action.can ? task.can[action.can] : true)}
            />

            <DeleteDialog
                title="Delete Task"
                url={route('tasks.destroy', {task})}
                isOpen={deleteDialogIsOpen}
                setIsOpen={setDeleteDialogIsOpen}
            >
                Are you sure you want to delete this task? This may break your onboarding process! This action cannot be undone.
            </DeleteDialog>

            <div className="py-6 px-4 sm:px-6 lg:px-8">

                <SectionHeader>
                    <SectionTitle>Notifications</SectionTitle>

                    <ButtonLink
                        variant="primary"
                        size="sm"
                        href={route('tasks.notifications.create', {task})}
                    >
                        <Icon icon="edit" mr />Add Notification
                    </ButtonLink>
                </SectionHeader>

            </div>
            <div className="h-full overflow-y-auto relative">
                {task.notification_templates.length > 0
                    ? (
                        <>
                            <div className="z-10 sticky top-0 border-t border-b border-gray-200 bg-gray-50 px-6 py-1 text-sm font-medium text-gray-500">
                                <h3>Notifications</h3>
                            </div>
                            <ul role="list" className="relative z-0 divide-y divide-gray-200">
                                {task.notification_templates.map((notification) => (
                                    <li key={notification.id} >
                                        <div className="relative px-6 py-5 flex items-center space-x-3">
                                            <div className="flex flex-wrap items-center justify-between px-4 w-full ms:space-x-2 space-y-2 md:space-y-0">
                                                <span className="text-sm font-medium truncate">
                                                    <Link
                                                        href={route('tasks.notifications.show', { task: task, notification: notification.id })}
                                                        className="text-purple-600"
                                                    >
                                                        {notification.subject}
                                                    </Link>
                                                </span>
                                                <span className="text-xs text-gray-500">
                                                    {notification.recipients}
                                                </span>
                                                <span className="text-xs text-gray-500">
                                                    {notification.delay}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </>
                    ) : (
                        <EmptyState
                            title="No task notifications"
                            description={<>
                                Task notifications allow you to send reminders and resources to prospects and team members when a task is completed. <br />
                                Looks like you haven't set up notifications.
                            </>}
                            actionDescription={'For a task like "Pass Audition", you could create a notification like "Congratulations", or "Please invoice".'}
                            icon="bells"
                            href={route('tasks.notifications.create', {task})}
                            actionLabel="Add Notification"
                            actionIcon="plus"
                        />
                    )
                }
            </div>
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;