import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
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
import AppHead from '../../../components/AppHead';
import { usePage } from '@inertiajs/react';
import Calendar from './../Calendar';
import useRoute from '../../../hooks/useRoute';

const Month = ({ days, month, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();
	const actions = [
		{
			label: 'Add New',
			icon: 'calendar-plus',
			href: route('events.create'),
			variant: 'primary',
			can: can.create_event,
		},
		{
			label: 'Attendance Report',
			icon: 'analytics',
			href: route('events.reports.attendance'),
			can: can.list_attendances,
		},
		{ label: 'List View', icon: 'th-list', href: route('events.index'), can: can.list_events },
	].filter(action => action.can);

	const breadcrumbs = [
		{ name: 'Events', url: route('events.index') },
		{ name: 'Calendar', url: route('events.calendar.month') },
	];

	return (
		<>
			<AppHead title="Calendar - Month View" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem key={action.label} url={action.href} variant={action.variant}>
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
						<Icon icon="calendar-alt" type="solid" className="mr-2" />
						Calendar
					</PageHeaderTitle>
					<PageHeaderMeta>
						<div>Calendar Sync URL: {route('events.feed')}</div>
					</PageHeaderMeta>
				</PageHeaderContent>
				<PageHeaderActions>
					{actions.map(action => (
						<Button key={action.label} href={action.href} size="sm" variant={action.variant}>
							<Icon icon={action.icon} mr />
							{action.label}
						</Button>
					))}
				</PageHeaderActions>
			</PageHeader>

			<Calendar days={days} month={month} />
		</>
	);
};

Month.layout = page => <TenantLayout children={page} />;

export default Month;
