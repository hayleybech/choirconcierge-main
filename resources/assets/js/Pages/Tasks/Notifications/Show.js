import React, { useState } from 'react';
import AppHead from '../../../components/AppHead';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/Button';
import Icon from '../../../components/Icon';
import DateTag from '../../../components/DateTag';
import SectionHeader from '../../../components/SectionHeader';
import SectionTitle from '../../../components/SectionTitle';
import TenantLayout from '../../../Layouts/TenantLayout';
import DeleteDialog from '../../../components/DeleteDialog';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';

const Show = ({ task, notification, setSidebarOpen }) => {
	const { route } = useRoute();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const actions = [
		{
			label: 'Edit',
			icon: 'edit',
			url: route('tasks.notifications.edit', { task, notification }),
			can: task.can.update_task,
		},
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: task.can.delete_task,
		},
	].filter(action => action.can);

	return (
		<>
			<AppHead title={`${notification.subject} - Task Notifications for "${task.name}"`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('tasks.show', { task })}
						breadcrumbs={[
							{ name: 'Onboarding', url: route('tasks.index') },
							{ name: task.name, url: route('tasks.show', { task }) },
						]}
					>
						<PageTopBarTitle title={notification.subject} />
					</PageTopNavigation>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</div>
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
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="bell" type="solid" className="mr-2" />
							{notification.subject}
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							<div>Recipients: {notification.recipients}</div>
							<div>Delay: {notification.delay}</div>
							<DateTag icon="pencil" date={notification.created_at} label="Created" />
							<DateTag icon="pencil" date={notification.updated_at} label="Updated" />
						</div>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{actions.map(action => (
							<Button
								key={action.label}
								href={action.url}
								onClick={action.onClick}
								size="sm"
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</Button>
						))}
					</div>
				</div>
			</PageHeader2>

			<DeleteDialog
				title="Delete Notifications"
				url={route('tasks.notifications.destroy', { task, notification })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this task notification? This may break your onboarding process! This
				action cannot be undone.
			</DeleteDialog>

			<div className="py-6 px-4 sm:px-6 lg:px-8">
				<SectionHeader>
					<SectionTitle>Body</SectionTitle>
				</SectionHeader>

				<Prose content={notification.body} className="mb-8" />
			</div>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;
