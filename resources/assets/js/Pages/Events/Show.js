import React, { useState } from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
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
import CollapseGroup from '../../components/CollapseGroup';
import EventType from '../../EventType';
import EventSchedule from '../../components/Event/EventSchedule';
import useRoute from '../../hooks/useRoute';
import { DateTime } from 'luxon';
import RsvpDropdown from '../../components/Event/RsvpDropdown';

const Show = ({
	event,
	rsvpCount,
	voicePartsRsvpCount,
	attendanceCount,
	voicePartsAttendanceCount,
	addToCalendarLinks,
}) => {
	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);

	const { route } = useRoute();
	const { props: pageProps } = usePage();

	return (
		<>
			<AppHead title={`${event.title} - Events`} />
			<PageHeader
				title={
					<>
						{event.title}
						{event.is_repeating && (
							<Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />
						)}
					</>
				}
				meta={<>

					{DateTime.fromISO(event.start_date).hasSame(DateTime.fromISO(event.end_date), 'day') ? (
						<div className="text-lg font-bold">
							<Icon icon="calendar-day" type="regular" mr className="text-gray-500" />
							<span>
								{DateTime.fromISO(event.start_date).toLocaleString(DateTime.DATETIME_MED)}
							</span>
							{' - '}
							<span>
								{DateTime.fromISO(event.end_date).toLocaleString(DateTime.TIME_SIMPLE)}
							</span>
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

					<div>
						<Badge colour={new EventType(event.type.title).badgeColour}>{event.type.title}</Badge>
					</div>

					{DateTime.fromISO(event.call_time) > DateTime.now() && (
						<RsvpDropdown event={event} size="xs" />
					)}

					<DateTag label="Arrive" date={event.call_time} format="TIME_SIMPLE" />

					{event.is_repeating && (
						<div>
							<Icon icon="repeat" mr className="text-gray-400" /> Repeat every {event.repeat_frequency_unit} until{' '}
							{DateTime.fromISO(event.repeat_until).toLocaleString(DateTime.DATE_MED)}
						</div>
					)}
					<div className="flex items-center gap-2">
						<DateTag icon="pencil" date={event.created_at} label="Created" />
						<DateTag icon="pencil" date={event.updated_at} label="Updated" />
					</div>
				</>}
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Events', url: route('events.index') },
					{ name: event.title, url: route('events.show', { event }) },
				]}
				actions={[
					event.is_repeating
						? { label: 'Edit', icon: 'edit', onClick: () => setEditDialogIsOpen(true), can: 'update_event' }
						: { label: 'Edit', icon: 'edit', url: route('events.edit', { event }), can: 'update_event' },
					{ label: 'Duplicate', icon: 'copy', url: route('events.clone', event), can: 'create_event'},
					{
						label: 'Delete',
						icon: 'trash',
						onClick: () => setDeleteDialogIsOpen(true),
						variant: 'danger-outline',
						can: 'delete_event',
					},
				].filter(action => (action.can ? event.can[action.can] || pageProps.can[action.can] : true))}
			/>

			<DeleteDialog
				title="Delete Event"
				url={route('events.destroy', { event })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this event? This action cannot be undone.
			</DeleteDialog>

			<EditRepeatingEventDialog isOpen={editDialogIsOpen} setIsOpen={setEditDialogIsOpen} event={event} />

			<div className="flex flex-col sm:grid sm:grid-cols-2 xl:grid-cols-4 h-full divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">
				<div className="sm:col-span-1 xl:col-span-3 divide-y divide-y-gray-300">
					<CollapseGroup
						items={[
							{
								title: 'Description',
								show: true,
								defaultOpen: event.description?.length > 0,
								content: (
									<EventDescription
										description={event.description}
										timezone={pageProps.tenant.timezone_label}
									/>
								),
							},
							{
								title: 'Location',
								show: true,
								defaultOpen: true,
								content: <EventLocation event={event} />,
							},
							{ title: 'Schedule', show: true, content: <EventSchedule event={event} /> },
						]}
					/>
				</div>

				<div className="sm:col-span-1 divide-y divide-y-gray-300">
					<CollapseGroup
						items={[
							{
								title: 'My Attendance',
								show: true,
								content: <MyAttendance event={event} addToCalendarLinks={addToCalendarLinks} />,
							},
							{
								title: 'RSVPs',
								show: pageProps.can['list_attendances'],
								action: <ViewRsvpsButton event={event} />,
								content: (
									<RsvpSummary
										event={event}
										rsvpCount={rsvpCount}
										voicePartsRsvpCount={voicePartsRsvpCount}
									/>
								),
							},
							{
								title: 'Attendance',
								show: pageProps.can['create_attendance'],
								action: <EditAttendanceButton event={event} />,
								content: (
									<AttendanceSummary
										event={event}
										attendanceCount={attendanceCount}
										voicePartsAttendanceCount={voicePartsAttendanceCount}
									/>
								),
							},
						]}
					/>
				</div>
			</div>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;

const EventDescription = ({ description, timezone }) => (
	<CollapsePanel>
		<Prose content={description} className="mb-8" />

		<p className="text-sm text-gray-500 my-2">Choir's Timezone: {timezone}</p>
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
