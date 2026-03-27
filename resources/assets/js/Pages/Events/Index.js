import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import EventTableDesktop from './EventTableDesktop';
import EventTableMobile from './EventTableMobile';
import EventFilters from '../../components/Event/EventFilters';
import IndexContainer from '../../components/IndexContainer';
import FilterSortPane from '../../components/FilterSortPane';
import useFilterPane from '../../hooks/useFilterPane';
import Sorts from '../../components/Sorts';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import BulkEditEventsModal from './BulkEditEventsModal';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({ events, eventTypes, userEnsemblesCount, ensembles, can }) => {
	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(events.data, can.update_event, can.delete_event, 'Event');

	const sorts = [
		{ id: 'title', name: 'Title' },
		{ id: 'start_date', name: 'Event Date', default: true },
		{ id: 'type-title', name: 'Type' },
		{ id: 'created_at', name: 'Date Created' },
	];

	const filters = [
		{ name: 'title', defaultValue: '' },
		{ name: 'type.id', multiple: true },
		{ name: 'date', defaultValue: 'upcoming' },
		{ name: 'ensembles.id', multiple: true },
	];

	const transforms = data => ({
		date: data.date === 'all' ? null : data.date,
	});

	const sortFilterForm = useSortFilterForm('events.index', filters, sorts, transforms);

	const actions = [
		{
			label: 'Add New',
			icon: 'calendar-plus',
			url: route('events.create'),
			variant: 'primary',
			can: 'create_event',
		},
		{ label: 'Event Types', icon: 'tags', url: route('event-types.index'), can: 'list_events' },
		{
			label: 'Attendance Report',
			icon: 'analytics',
			url: route('events.reports.attendance'),
			can: 'list_attendances',
		},
		{ label: 'Calendar View', icon: 'calendar-alt', url: route('events.calendar.month') },
		bulkEdit.action,
		filterAction,
	]
		.filter(action => !!action)
		.filter(action => (action.can ? can[action.can] : true));

	return (
		<>
			<AppHead title="Events" />
			<PageHeader
				title="Events"
				icon="calendar"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Events', url: route('events.index') },
				]}
				actions={actions}
				meta={<div>Calendar Sync URL: {route('events.feed')}</div>}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Events?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okVariant="danger-solid"
				okMethod="post"
				data={{ event_ids: bulkEdit.selectedIds }}
				okUrl={route('events.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected events? This action cannot be undone.
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
						filters={
							<EventFilters
								eventTypes={eventTypes}
								ensembles={ensembles}
								userEnsemblesCount={userEnsemblesCount}
								form={sortFilterForm}
							/>
						}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={
					<EventTableMobile
						events={events}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
						setShowFilters={setShowFilters}
						hasNonDefaultFilters={hasNonDefaultFilters}
					/>
				}
				tableDesktop={
					<EventTableDesktop
						events={events}
						sortFilterForm={sortFilterForm}
						userEnsemblesCount={userEnsemblesCount}
						bulkEdit={bulkEdit}
					/>
				}
				emptyState={
					events.data.length === 0 ? (
						<EmptyState
							title="No events"
							actionDescription={
								can['create_event']
									? 'Get started by adding an event like a rehearsal or performance. Otherwise, try expanding your filtering options.'
									: 'Your team might not have added any events yet. Otherwise, you may need to expand your filtering options.'
							}
							icon="calendar"
							href={can['create_event'] ? route('events.create') : null}
							actionLabel="Add Event"
							actionIcon="calendar-plus"
						/>
					) : null
				}
			/>

			<BulkEditEventsModal
				isOpen={bulkEdit.showEditModal}
				setIsOpen={bulkEdit.setShowEditModal}
				selectedEventIds={bulkEdit.selectedIds}
				key={bulkEdit.selectedIds}
				onSuccess={() => bulkEdit.setSelectedIds([])}
				eventTypes={eventTypes}
				ensembles={ensembles}
				userEnsemblesCount={userEnsemblesCount}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
