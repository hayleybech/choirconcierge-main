import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderLink,
	PageHeaderLinks,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import EventTableDesktop from './EventTableDesktop';
import EventTableMobile from './EventTableMobile';
import EventFilters from '../../components/Event/EventFilters';
import IndexContainer from '../../components/IndexContainer';
import IndexTableControls from '../../components/IndexTableControls';
import FilterDrawer from '../../components/FilterDrawer';
import useFilterPane from '../../hooks/useFilterPane';
import useSortFilterForm from '../../hooks/useSortFilterForm';
import EmptyState from '../../components/EmptyState';
import useRoute from '../../hooks/useRoute';
import BulkEditEventsModal from './BulkEditEventsModal';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';
import CalendarSyncDialog from '../../components/Event/CalendarSyncDialog';
import ButtonLink from '../../components/inputs/ButtonLinkV2';

const Index = ({ events, eventTypes, userEnsemblesCount, ensembles, calendarSyncUrl, can, setSidebarOpen }) => {
	const [showFilters, setShowFilters] = useFilterPane();
	const [showSyncModal, setShowSyncModal] = React.useState(false);
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(events.data, can.update_event, can.delete_event, 'Event');

	const sorts = [
		{ id: 'title', name: 'Title' },
		{ id: 'start_date', name: 'Event Date', default: true },
		{ id: 'type-title', name: 'Type' },
		{ id: 'created_at', name: 'Date Created' },
	];

	const filters = [
		{ name: 'title', label: 'Title', type: 'text', defaultValue: '' },
		{
			name: 'type.id',
			label: 'Type',
			multiple: true,
			options: eventTypes.map(type => ({ id: type.id, name: type.title })),
		},
		{
			name: 'date',
			label: 'Date',
			defaultValue: 'upcoming',
			options: [
				{ id: 'all', name: 'All' },
				{ id: 'upcoming', name: 'Upcoming' },
				{ id: 'past', name: 'Past' },
			],
			onChange: (value, form) => {
				form.setData('date', value);

				if (value === 'upcoming') {
					form.setData('sort', 'start_date');
					form.setData('sortDir', 'asc');
				}

				if (value === 'past') {
					form.setData('sort', 'start_date');
					form.setData('sortDir', 'desc');
				}
			},
		},
		...(userEnsemblesCount > 1
			? [
					{
						name: 'ensembles.id',
						label: 'Ensemble',
						multiple: true,
						options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
					},
			  ]
			: []),
	];

	const transforms = data => ({
		date: data.date === 'all' ? null : data.date,
	});

	const sortFilterForm = useSortFilterForm('events.index', filters, sorts, transforms);

	const actions = [
		{
			label: 'Add New',
			icon: 'calendar-plus',
			href: route('events.create'),
			intent: 'primary',
			weight: 'solid',
			can: 'create_event',
		},
		{ label: 'Event Types', icon: 'tags', href: route('event-types.index'), can: 'list_events' },
		{
			label: 'Attendance Report',
			icon: 'analytics',
			href: route('events.reports.attendance'),
			can: 'list_attendances',
		},
		{ label: 'Calendar View', icon: 'calendar-alt', href: route('events.calendar.month') },
		{
			label: 'Sync to Calendar App',
			icon: 'calendar-plus',
			onClick: () => setShowSyncModal(true),
		},
		bulkEdit.action,
	]
		.filter(action => !!action)
		.filter(action => (action.can ? can[action.can] : true));

	const links = actions.filter(action =>
		['Event Types', 'Attendance Report', 'Calendar View'].includes(action.label)
	);
	const pageActions = actions.filter(action => !links.includes(action));
	const pageMenuActions = actions.filter(action => action !== bulkEdit.action);

	return (
		<>
			<AppHead title="Events" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Events">
					<PageActionsMenu>
						{pageMenuActions.map((action, key) => (
							<ActionMenuItem
								key={key}
								url={action.href}
								onClick={action.onClick}
								download={action.download}
								intent={action.intent}
								method={action.method}
								disabled={action.disabled}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</PageTopNavigation>
			</PageTopBar>

			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<Icon icon="calendar" type="solid" className="mr-2" /> Events
						</PageHeaderTitle>
						<PageHeaderLinks>
							{links.map(action => (
								<PageHeaderLink key={action.label} {...action}>
									{action.label}
								</PageHeaderLink>
							))}
						</PageHeaderLinks>
						<PageHeaderActions>
							{pageActions.map(action => (
								<ButtonLink key={action.label} {...action} size="md" weight={action.weight ?? 'clear'}>
									<Icon icon={action.icon} mr />
									{action.label}
								</ButtonLink>
							))}
						</PageHeaderActions>
					</PageHeaderTitleRow>
				</PageHeaderContent>
			</PageHeader>

			<CalendarSyncDialog calendarSyncUrl={calendarSyncUrl} isOpen={showSyncModal} setIsOpen={setShowSyncModal} />

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Events?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okColour="danger"
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
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						bulkEdit={bulkEdit}
						filterLabel="events"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<EventFilters
							eventTypes={eventTypes}
							ensembles={ensembles}
							userEnsemblesCount={userEnsemblesCount}
							form={sortFilterForm}
						/>
					</FilterDrawer>
				}
				tableMobile={
					<EventTableMobile events={events} userEnsemblesCount={userEnsemblesCount} bulkEdit={bulkEdit} />
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
				selectedEvents={events.data.filter(e => bulkEdit.selectedIds.includes(e.id))}
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
