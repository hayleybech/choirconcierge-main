import React from 'react';
import AppHead from '../../components/AppHead';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import TenantLayout from '../../Layouts/TenantLayout';
import TaskForm from './TaskForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ roles, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Create Task" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('tasks.index')}
					breadcrumbs={[{ name: 'Onboarding', url: route('tasks.index') }]}
				>
					<PageTopBarTitle title="Create Task" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Onboarding', url: route('tasks.index') },
									{ name: 'Create', url: route('tasks.create') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="tasks" type="solid" className="mr-2" />
							Create Task
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<TaskForm roles={roles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
