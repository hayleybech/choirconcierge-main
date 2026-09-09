import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle } from '../../components/PageTopBar';
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
				<div className="flex justify-between grow">
					<PageTopBarTitle title="Onboarding" />
					<PageActionsMenu>
						{can.create_task && (
							<ActionMenuItem url={route('tasks.create')} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						)}
					</PageActionsMenu>
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<PageHeading>
							<Icon icon="tasks" type="solid" className="mr-2" /> Onboarding Tasks
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{can.create_task && (
							<Button href={route('tasks.create')} size="sm" variant="primary">
								<Icon icon="plus" mr />
								Add New
							</Button>
						)}
					</div>
				</div>
			</PageHeader2>

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
