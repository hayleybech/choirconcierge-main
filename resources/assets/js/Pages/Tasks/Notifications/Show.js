import React, {useState} from 'react'
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import DateTag from "../../../components/DateTag";
import SectionHeader from "../../../components/SectionHeader";
import SectionTitle from "../../../components/SectionTitle";
import Dialog from "../../../components/Dialog";
import Layout from "../../../Layouts/Layout";

const Show = ({ task, notification }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);

    return (
        <>
            <AppHead title={`${notification.subject} - Task Notifications for "${task.name}"`} />
            <PageHeader
                title={notification.subject}
                meta={[
                    <>Recipients: {notification.recipients}</>,
                    <>Delay: {notification.delay}</>,
                    <DateTag date={notification.created_at} label="Created" />,
                    <DateTag date={notification.updated_at} label="Updated" />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Tasks', url: route('tasks.index')},
                    { name: task.name, url: route('tasks.show', task) },
                    { name: notification.subject, url: route('tasks.notifications.show', [task, notification]) },
                ]}
                actions={[
                    { label: 'Edit', icon: 'edit', url: route('tasks.notifications.edit', [task, notification]), can: 'update_task' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_task' },
                ].filter(action => action.can ? task.can[action.can] : true)}
            />

            <DeleteNotificationDialog isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen} task={task} notification={notification} />

            <div className="py-6 px-4 sm:px-6 lg:px-8">

                <SectionHeader>
                    <SectionTitle>Body</SectionTitle>
                </SectionHeader>

                <div dangerouslySetInnerHTML={{ __html: notification.body_with_highlights }} />
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;

const DeleteNotificationDialog = ({ isOpen, setIsOpen, task, notification }) => (
    <Dialog
        title="Delete Notification"
        okLabel="Delete"
        okUrl={route('tasks.destroy', [task, notification])}
        okVariant="danger-solid"
        okMethod="delete"
        isOpen={isOpen}
        setIsOpen={setIsOpen}
    >
        <p>
            Are you sure you want to delete this task notification? This may break your onboarding process! This action cannot be undone.
        </p>
    </Dialog>
);