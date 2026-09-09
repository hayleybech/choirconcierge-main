import React from 'react';
import AppHead from '../../../components/AppHead';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import TenantLayout from '../../../Layouts/TenantLayout';
import TaskNotificationForm from './TaskNotificationForm';
import useRoute from '../../../hooks/useRoute';

const Edit = ({ task, notification, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${notification.subject}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('tasks.notifications.show', { task, notification })}
					breadcrumbs={[
						{ name: 'Onboarding', url: route('tasks.index') },
						{ name: task.name, url: route('tasks.show', { task }) },
						{ name: notification.subject, url: route('tasks.notifications.show', { task, notification }) },
					]}
				>
					<PageTopBarTitle title="Edit Notification" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Onboarding', url: route('tasks.index') },
									{ name: task.name, url: route('tasks.show', { task }) },
									{
										name: notification.subject,
										url: route('tasks.notifications.show', { task, notification }),
									},
									{ name: 'Edit', url: route('tasks.notifications.edit', { task, notification }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="bell" type="solid" className="mr-2" />
							Edit Notification
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<TaskNotificationForm task={task} notification={notification} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
