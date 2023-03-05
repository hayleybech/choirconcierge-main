import React, {useState} from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import DateTag from "../../components/DateTag";
import Badge from "../../components/Badge";
import GoogleMap from "../../components/GoogleMap";
import MyAttendance from "../../components/Event/MyAttendance";
import RsvpSummary from "../../components/Event/RsvpSummary";
import AttendanceSummary from "../../components/Event/AttendanceSummary";
import {usePage} from "@inertiajs/inertia-react";
import Icon from "../../components/Icon";
import EditRepeatingEventDialog from "../../components/Event/EditRepeatingEventDialog";
import DeleteDialog from "../../components/DeleteDialog";
import Prose from "../../components/Prose";
import ButtonLink from "../../components/inputs/ButtonLink";
import CollapsePanel from "../../components/CollapsePanel";
import CollapseGroup from "../../components/CollapseGroup";
import EventType from "../../EventType";
import EventSchedule from "../../components/Event/EventSchedule";

const Show = ({ event, rsvpCount, voicePartsRsvpCount, attendanceCount, voicePartsAttendanceCount, addToCalendarLinks }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);

    const { props: pageProps } = usePage();

    return (
        <>
            <AppHead title={`${event.title} - Events`} />
            <PageHeader
                title={<>{event.title}{event.is_repeating && <Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />}</>}
                meta={[
                    <Badge colour={(new EventType(event.type.title)).badgeColour}>{event.type.title}</Badge>,
                    <DateTag label="Start" date={event.call_time} format="DATETIME_MED" />,
                    <DateTag label="End" date={event.end_date} format="DATETIME_MED" />, // shorter for same day
                    <DateTag label="On Stage" date={event.start_date} format="TIME_SIMPLE" />, // hide unless music team
                    <DateTag label="Created" date={event.created_at} />,
                    event.is_repeating && <><Icon icon="repeat" mr /> Repeat every {event.repeat_frequency_unit}</>,
                    event.is_repeating && <DateTag label="Repeat until" date={event.repeat_until} />,
                ]}
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Events', url: route('events.index')},
                    { name: event.title, url: route('events.show', event) },
                ]}
                actions={[
                    event.is_repeating
                        ? { label: 'Edit', icon: 'edit', onClick: () => setEditDialogIsOpen(true), can: 'update_event' }
                        : { label: 'Edit', icon: 'edit', url: route('events.edit', event), can: 'update_event' },
                    { label: 'Delete', icon: 'trash', onClick: () => setDeleteDialogIsOpen(true), variant: 'danger-outline', can: 'delete_event' },
                ].filter(action => action.can ? event.can[action.can] : true)}
            />

            <DeleteDialog title="Delete Event" url={route('events.destroy', event)} isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen}>
                Are you sure you want to delete this event? This action cannot be undone.
            </DeleteDialog>

            <EditRepeatingEventDialog isOpen={editDialogIsOpen} setIsOpen={setEditDialogIsOpen} event={event} />

            <div className="flex flex-col sm:grid sm:grid-cols-2 xl:grid-cols-4 h-full divide-y divide-gray-300 sm:divide-y-0 sm:divide-x">

                <div className="sm:col-span-1 xl:col-span-3 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        { title: 'Description', show: true, content: <EventDescription description={event.description} timezone={pageProps.tenant.timezone_label} />},
                        { title: 'Location', show: true, defaultOpen: true, content: <EventLocation event={event} />},
                        { title: 'Schedule', show: true, content: <EventSchedule event={event} />},
                    ]} />
                </div>

                <div className="sm:col-span-1 divide-y divide-y-gray-300">
                    <CollapseGroup items={[
                        { title: 'My Attendance', show: true, content: <MyAttendance event={event} addToCalendarLinks={addToCalendarLinks} />},
                        { title: 'RSVP Summary', show: pageProps.can['list_attendances'], content: <RsvpSummary event={event} rsvpCount={rsvpCount} voicePartsRsvpCount={voicePartsRsvpCount} /> },
                        {
                            title: 'Attendance Summary',
                            show: pageProps.can['create_attendance'],
                            action: <EditAttendanceButton event={event} />,
                            content: <AttendanceSummary event={event} attendanceCount={attendanceCount} voicePartsAttendanceCount={voicePartsAttendanceCount}/>,
                        },
                    ]} />
                </div>

            </div>
        </>
    );
}

Show.layout = page => <TenantLayout children={page} />

export default Show;

const EventDescription = ({ description, timezone }) => (
    <CollapsePanel>
        <Prose content={description} className="mb-8" />

        <p className="text-sm text-gray-500 my-2">Choir's Timezone: {timezone}</p>
    </CollapsePanel>
);

const EventLocation = ({ event }) => (
    <CollapsePanel>
        <p><strong>{event.location_name}</strong></p>
        <p className="mb-8">{event.location_address}</p>

        <GoogleMap placeId={event.location_place_id} />
    </CollapsePanel>
);


const EditAttendanceButton = ({ event }) => (
    <ButtonLink variant="primary" size="sm" href={route('events.attendances.index', [event])}>
        <Icon icon="edit" />
        Edit
    </ButtonLink>
);