import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import DateTag from '../../components/DateTag';
import Badge from '../../components/Badge';
import GoogleMap from '../../components/GoogleMap';
import MyAttendance from '../../components/Event/MyAttendance';
import RsvpSummary from '../../components/Event/RsvpSummary';
import AttendanceSummary from '../../components/Event/AttendanceSummary';
import { usePage } from '@inertiajs/react';
import Icon from '../../components/Icon';
import EditRepeatingEventDialog from '../../components/Event/EditRepeatingEventDialog';
import DeleteDialog from '../../components/DeleteDialog';
import Prose from '../../components/Prose';
import ButtonLink from '../../components/inputs/ButtonLink';
import CollapsePanel from '../../components/CollapsePanel';
import SectionLayout from '../Singers/SectionLayout';
import EventType from '../../EventType';
import EventSchedule from '../../components/Event/EventSchedule';
import useRoute from '../../hooks/useRoute';
import { DateTime } from 'luxon';
import RsvpDropdown from '../../components/Event/RsvpDropdown';
import AddToCalendarDropdown from '../../components/Event/AddToCalendarDropdown';

const Show = ({
	event,
	rsvpCount,
	voicePartsRsvpCount,
	attendanceCount,
	voicePartsAttendanceCount,
	addToCalendarLinks,
	setSidebarOpen,
}) => {
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);

	const { route } = useRoute();
	const { props: pageProps } = usePage();
	const actions = [
		{
			label: 'Edit',
			icon: 'edit',
			url: event.is_repeating ? undefined : route('events.edit', { event }),
			onClick: event.is_repeating ? () => setEditDialogIsOpen(true) : undefined,
			can: 'update_event',
		},
		{ label: 'Duplicate', icon: 'copy', url: route('events.clone', { event }), can: 'create_event' },
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			variant: 'danger-outline',
			can: 'delete_event',
		},
	].filter(action => event.can[action.can] || pageProps.can[action.can]);

	return (
		<>
			<AppHead title={`${event.title} - Events`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						breadcrumbs={[{ name: 'Events', url: route('events.index') }]}
						backUrl={route('events.index')}
					>
						<PageTopBarTitle title={event.title} />
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
									{ name: 'Events', url: route('events.index') },
									{ name: event.title, url: route('events.show', { event }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<span className="flex items-center">
								{event.title}
								{event.is_repeating && (
									<Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />
								)}
							</span>
						</PageHeading>
						<div className="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-2 gap-2 sm:gap-6 text-sm sm:items-center text-gray-500">
							{DateTime.fromISO(event.start_date).hasSame(DateTime.fromISO(event.end_date), 'day') ? (
								<div className="text-lg font-bold">
									<Icon icon="calendar-day" type="regular" mr className="text-gray-500" />
									<span>
										{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATETIME_MED)}
									</span>
									{' - '}
									<span>{DateTime.fromISO(event.end_date).toLocaleString(DateTime.TIME_SIMPLE)}</span>
								</div>
							) : (
								<div className="text-lg font-bold flex items-center">
									<Icon icon="calendar-day" type="regular" mr />
									<div>
										<span className="whitespace-nowrap">
											{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATETIME_MED)}
										</span>
										{' - '}
										<span className="whitespace-nowrap">
											{DateTime.fromISO(event.end_date).toLocaleString(DateTime.DATETIME_MED)}
										</span>
									</div>
								</div>
							)}
							<DateTag label="Arrive" date={event.call_time} format="TIME_SIMPLE" />
							<div>
								<Badge colour={new EventType(event.type.title).badgeColour}>{event.type.title}</Badge>
							</div>
							{event.ensembles.length > 0 && (
								<div className="space-x-1.5 flex items-center">
									{event.ensembles.map(ensemble => (
										<Badge key={ensemble.id} colour="bg-blue-100 text-blue-800">
											{ensemble.name}
										</Badge>
									))}
								</div>
							)}
							<div className="gap-1.5 grid sm:inline-flex grid-cols-2">
								{DateTime.fromISO(event.call_time) > DateTime.now() && (
									<RsvpDropdown event={event} size="sm" />
								)}
								<AddToCalendarDropdown urls={addToCalendarLinks} size="sm" />
							</div>
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
				title="Delete Event"
				url={route('events.destroy', { event })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this event? This action cannot be undone.
			</DeleteDialog>

			<EditRepeatingEventDialog isOpen={editDialogIsOpen} setIsOpen={setEditDialogIsOpen} event={event} />

			<SectionLayout
				gridClassName="grid-cols-1 h-full divide-y divide-gray-300 sm:grid sm:grid-cols-2 sm:divide-x sm:divide-y-0 md:grid-cols-3 xl:grid-cols-4"
				columnClassNames={[
					'sm:col-span-1 md:col-span-2 xl:col-span-3 divide-y divide-gray-300',
					'sm:col-span-1 divide-y divide-gray-300',
				]}
				columns={[
					[
						{
							title: 'Summary',
							show: true,
							defaultOpen: event.description?.length > 0,
							content: <EventSummary event={event} timezone={pageProps.tenant.timezone_label} />,
						},
						{
							title: 'Location',
							show: true,
							defaultOpen: true,
							content: <EventLocation event={event} />,
						},
						{ title: 'Schedule', show: true, content: <EventSchedule event={event} /> },
					],
					[
						{
							title: 'Attendance',
							key: 'Attendance',
							hideTitleOnDesktop: true,
							show: true,
							content: (
								<>
									<div className="py-2 px-4 bg-gray-100 border-b border-gray-200">
										<h3 className="font-semibold text-gray-700">My Attendance</h3>
									</div>
									<MyAttendance event={event} addToCalendarLinks={addToCalendarLinks} />
									{pageProps.can['list_attendances'] && (
										<>
											<hr className="border-gray-200" />
											<div className="py-2 px-4 bg-gray-100 border-b border-gray-200 flex items-center justify-between gap-2">
												<h3 className="font-semibold text-gray-700">RSVPs</h3>
												<ViewRsvpsButton event={event} />
											</div>

											<RsvpSummary
												event={event}
												rsvpCount={rsvpCount}
												voicePartsRsvpCount={voicePartsRsvpCount}
											/>
										</>
									)}
									{pageProps.can['create_attendance'] && (
										<>
											<hr className="border-gray-200" />
											<div className="py-2 px-4 bg-gray-100 border-b border-gray-200 flex items-center justify-between gap-2">
												<h3 className="font-semibold text-gray-700">Attendance</h3>
												<EditAttendanceButton event={event} />
											</div>

											<AttendanceSummary
												event={event}
												attendanceCount={attendanceCount}
												voicePartsAttendanceCount={voicePartsAttendanceCount}
											/>
										</>
									)}
								</>
							),
						},
					],
				]}
			/>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const EventSummary = ({ event, timezone }) => (
	<CollapsePanel>
		<h3 className="pt-4 font-semibold text-gray-700">Date/Time</h3>

		{DateTime.fromISO(event.start_date).hasSame(DateTime.fromISO(event.end_date), 'day') ? (
			<div className="font-bold">
				<Icon icon="calendar-day" type="regular" mr className="text-gray-500" />
				<span>{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATETIME_MED)}</span>
				{' - '}
				<span>{DateTime.fromISO(event.end_date).toLocaleString(DateTime.TIME_SIMPLE)}</span>
			</div>
		) : (
			<div className="font-bold flex items-center">
				<Icon icon="calendar-day" type="regular" mr />
				<div>
					<span className="whitespace-nowrap">
						{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATETIME_MED)}
					</span>
					{' - '}
					<span className="whitespace-nowrap">
						{DateTime.fromISO(event.end_date).toLocaleString(DateTime.DATETIME_MED)}
					</span>
				</div>
			</div>
		)}

		<DateTag label="Arrive" date={event.call_time} format="TIME_SIMPLE" />

		{event.is_repeating && (
			<div>
				<Icon icon="repeat" mr className="text-gray-400" /> Repeat every {event.repeat_frequency_unit} until{' '}
				{DateTime.fromISO(event.repeat_until).toLocaleString(DateTime.DATE_MED)}
			</div>
		)}
		<p className="text-sm text-gray-500 my-2">Choir's Timezone: {timezone}</p>

		<h3 className="pt-4 font-semibold text-gray-700">Description</h3>
		<Prose content={event.description} className="mb-8" />

		<div className="flex items-center justify-between gap-2 text-sm text-gray-500">
			<DateTag icon="pencil" date={event.created_at} label="Created" />
			<DateTag icon="pencil" date={event.updated_at} label="Updated" />
		</div>
	</CollapsePanel>
);

const EventLocation = ({ event }) => (
	<CollapsePanel>
		<p>
			<strong>{event.location_name}</strong>
		</p>
		<p className="mb-8">{event.location_address}</p>

		<GoogleMap placeId={event.location_place_id} />
	</CollapsePanel>
);

const ViewRsvpsButton = ({ event }) => {
	const { route } = useRoute();

	return (
		<ButtonLink variant="primary" size="xs" href={route('events.rsvps.index', { event })}>
			<Icon icon="clipboard-list" />
			View All
		</ButtonLink>
	);
};

const EditAttendanceButton = ({ event }) => {
	const { route } = useRoute();

	return (
		<ButtonLink variant="primary" size="xs" href={route('events.attendances.index', { event })}>
			<Icon icon="edit" />
			Edit
		</ButtonLink>
	);
};
