import React from 'react'
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import TenantLayout from "../../../Layouts/TenantLayout";
import TaskNotificationForm from "./TaskNotificationForm";
import useRoute from "../../../hooks/useRoute";

const Edit = ({ task, notification }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${notification.subject}`} />
			<PageHeader
				title="Edit Notification"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash')},
					{ name: 'Tasks', url: route('tasks.index')},
					{ name: task.name, url: route('tasks.show', {task}) },
					{ name: notification.subject, url: route('tasks.notifications.show', {task, notification}) },
					{ name: 'Edit', url: route('tasks.notifications.edit', {task, notification})},
				]}
			/>

			<TaskNotificationForm task={task} notification={notification} />
		</>
	);
}

Edit.layout = page => <TenantLayout children={page} />

export default Edit;