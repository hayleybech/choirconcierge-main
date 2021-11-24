import React from 'react'
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import Layout from "../../../Layouts/Layout";
import TaskNotificationForm from "./TaskNotificationForm";

const Edit = ({ task, notification }) => (
    <>
        <AppHead title={`Edit - ${notification.subject}`} />
        <PageHeader
            title="Edit Notification"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Tasks', url: route('tasks.index')},
                { name: task.name, url: route('tasks.show', task) },
                { name: notification.subject, url: route('tasks.notifications.show', [task, notification]) },
                { name: 'Edit', url: route('tasks.notifications.edit', [task, notification])},
            ]}
        />

        <TaskNotificationForm task={task} notification={notification} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;