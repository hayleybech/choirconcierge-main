import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import EventForm from './EventForm';
import useRoute from '../../hooks/useRoute';

const Edit = ({ event, types, mode, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Events', url: route('events.index') },
		{ name: event.title, url: route('events.show', { event }) },
		{ name: 'Edit Event', url: route('events.edit', { event }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${event.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="calendar-edit" type="solid" className="mr-2" />
						Edit Event
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<EventForm event={event} types={types} mode={mode} ensembles={ensembles} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
