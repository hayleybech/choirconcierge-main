import React from 'react'
import TaskNotificationForm from "./TaskNotificationForm";
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import Layout from "../../../Layouts/Layout";

const Create = ({ task }) => (
    <>
        <AppHead title="Create Task Notification" />
        <PageHeader
            title="Create Task Notification"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Tasks', url: route('tasks.index')},
                { name: task.name, url: route('tasks.show', task) },
                { name: 'Create Notification', url: route('tasks.notifications.create', task)},
            ]}
        />

        <TaskNotificationForm task={task} />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;