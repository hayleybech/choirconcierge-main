import React from 'react';
import AppHead from '../../components/AppHead';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import TenantLayout from '../../Layouts/TenantLayout';
import TaskForm from './TaskForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ roles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Onboarding', url: route('tasks.index') },
		{ name: 'Create Task', url: route('tasks.create') },
	];

	return (
		<>
			<AppHead title="Create Task" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="tasks" type="solid" className="mr-2" />
						Create Task
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<TaskForm roles={roles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
