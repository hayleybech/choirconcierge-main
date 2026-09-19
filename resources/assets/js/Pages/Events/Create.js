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

const Create = ({ types, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Events', url: route('events.index') },
		{ name: 'Create Event', url: route('events.create') },
	];

	return (
		<>
			<AppHead title="Create Event" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="calendar-plus" type="solid" className="mr-2" />
						Create Event
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<EventForm types={types} ensembles={ensembles} />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
