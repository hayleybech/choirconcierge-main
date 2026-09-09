import React from 'react';
import TaskNotificationForm from './TaskNotificationForm';
import AppHead from '../../../components/AppHead';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import TenantLayout from '../../../Layouts/TenantLayout';
import useRoute from '../../../hooks/useRoute';

const Create = ({ task, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Create Task Notification" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('tasks.show', { task })}
					breadcrumbs={[
						{ name: 'Onboarding', url: route('tasks.index') },
						{ name: task.name, url: route('tasks.show', { task }) },
					]}
				>
					<PageTopBarTitle title="Create Task Notification" />
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
									{ name: 'Create Notification', url: route('tasks.notifications.create', { task }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="bell-plus" type="solid" className="mr-2" />
							Create Task Notification
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<TaskNotificationForm task={task} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
