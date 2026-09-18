import React from 'react';
import AppHead from '../../../components/AppHead';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import TenantLayout from '../../../Layouts/TenantLayout';
import TaskNotificationForm from './TaskNotificationForm';
import useRoute from '../../../hooks/useRoute';

const Edit = ({ task, notification, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Onboarding', url: route('tasks.index') },
		{ name: task.name, url: route('tasks.show', { task }) },
		{ name: notification.subject, url: route('tasks.notifications.show', { task, notification }) },
		{ name: 'Edit Notification', url: route('tasks.notifications.edit', { task, notification }) },
	];
	return (
		<>
			<AppHead title={`Edit - ${notification.subject}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="bell" type="solid" className="mr-2" />
						Edit Notification
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<TaskNotificationForm task={task} notification={notification} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
