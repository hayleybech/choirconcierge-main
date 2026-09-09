import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import EventForm from './EventForm';
import useRoute from '../../hooks/useRoute';

const Edit = ({ event, types, mode, ensembles, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${event.title}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('events.show', { event })}
					breadcrumbs={[
						{ name: 'Events', url: route('events.index') },
						{ name: event.title, url: route('events.show', { event }) },
					]}
				>
					<PageTopBarTitle title="Edit Event" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Events', url: route('events.index') },
									{ name: event.title, url: route('events.show', { event }) },
									{ name: 'Edit', url: route('events.edit', { event }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="calendar-edit" type="solid" className="mr-2" />
							Edit Event
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<EventForm event={event} types={types} mode={mode} ensembles={ensembles} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
