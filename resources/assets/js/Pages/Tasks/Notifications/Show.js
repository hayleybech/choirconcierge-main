import React, { useState } from 'react';
import AppHead from '../../../components/AppHead';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
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

	const breadcrumbs = [
		{ name: 'Onboarding', url: route('tasks.index') },
		{ name: task.name, url: route('tasks.show', { task }) },
		{
			name: notification.subject,
			url: route('tasks.notifications.show', { task, notification }),
		},
	];

	return (
		<>
			<AppHead title={`${notification.subject} - Task Notifications for "${task.name}"`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
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
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="bell" type="solid" className="mr-2" />
						{notification.subject}
					</PageHeaderTitle>
					<PageHeaderMeta>
						<div>Recipients: {notification.recipients}</div>
						<div>Delay: {notification.delay}</div>
						<DateTag icon="pencil" date={notification.created_at} label="Created" />
						<DateTag icon="pencil" date={notification.updated_at} label="Updated" />
					</PageHeaderMeta>
				</PageHeaderContent>
				<PageHeaderActions>
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
				</PageHeaderActions>
			</PageHeader>

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
