import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import TaskTableDesktop from './TaskTableDesktop';
import TaskTableMobile from './TaskTableMobile';
import { usePage } from '@inertiajs/react';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import useRoute from '../../hooks/useRoute';

const Index = ({ tasks, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Onboarding" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Onboarding">
					<PageActionsMenu>
						{can.create_task && (
							<ActionMenuItem url={route('tasks.create')} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						)}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="tasks" type="solid" className="mr-2" /> Onboarding Tasks
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{can.create_task && (
						<Button href={route('tasks.create')} size="sm" variant="primary">
							<Icon icon="plus" mr />
							Add New
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				tableDesktop={<TaskTableDesktop tasks={tasks} />}
				tableMobile={<TaskTableMobile tasks={tasks} />}
				emptyState={
					tasks.data.length === 0 ? (
						<EmptyState
							title="No onboarding tasks"
							description={
								<>
									Onboarding tasks allow you to keep track of your recruitment process and send
									reminders and resources to prospects and team members. <br />
									Looks like there are no onboarding tasks set up yet.
								</>
							}
							actionDescription={
								can['create_task']
									? 'Create a task like "Pass Audition", then create some notifications e.g. "Congratulations".'
									: null
							}
							icon="tasks"
							href={can['create_task'] ? route('tasks.create') : null}
							actionLabel="Add Task"
							actionIcon="plus"
						/>
					) : null
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
