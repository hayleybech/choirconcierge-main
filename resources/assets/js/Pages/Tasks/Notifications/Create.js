import React from 'react';
import TaskNotificationForm from './TaskNotificationForm';
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
import useRoute from '../../../hooks/useRoute';

const Create = ({ task, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Onboarding', url: route('tasks.index') },
		{ name: task.name, url: route('tasks.show', { task }) },
		{ name: 'Create Task Notification', url: route('tasks.notifications.create', { task }) },
	];

	return (
		<>
			<AppHead title="Create Task Notification" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="bell-plus" type="solid" className="mr-2" />
						Create Task Notification
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<TaskNotificationForm task={task} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
