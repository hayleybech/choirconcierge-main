import React from 'react';
import TenantLayout from '../../../Layouts/TenantLayout';
import { PageHeader2 } from '../../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../../components/PageTopBar';
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

	return (
		<>
			<AppHead title="Calendar - Month View" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('events.index')}
						breadcrumbs={[{ name: 'Events', url: route('events.index') }]}
					>
						<PageTopBarTitle title="Calendar" />
					</PageTopNavigation>
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem key={action.label} url={action.href} variant={action.variant}>
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
									{ name: 'Events', url: route('events.index') },
									{ name: 'Calendar', url: route('events.calendar.month') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="calendar-alt" type="solid" className="mr-2" />
							Calendar
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							<div>Calendar Sync URL: {route('events.feed')}</div>
						</div>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{actions.map(action => (
							<Button key={action.label} href={action.href} size="sm" variant={action.variant}>
								<Icon icon={action.icon} mr />
								{action.label}
							</Button>
						))}
					</div>
				</div>
			</PageHeader2>

			<Calendar days={days} month={month} />
		</>
	);
};

Month.layout = page => <TenantLayout children={page} />;

export default Month;
