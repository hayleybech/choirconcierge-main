import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import Dialog from "../../components/Dialog";
import AppHead from "../../components/AppHead";
import DateTag from "../../components/DateTag";
import Badge from "../../components/Badge";
import SectionTitle from "../../components/SectionTitle";
import GoogleMap from "../../components/GoogleMap";
import MyAttendance from "../../components/Event/MyAttendance";
import RsvpSummary from "../../components/Event/RsvpSummary";
import AttendanceSummary from "../../components/Event/AttendanceSummary";
import SectionHeader from "../../components/SectionHeader";
import {usePage} from "@inertiajs/inertia-react";
import Icon from "../../components/Icon";
import EditRepeatingEventDialog from "../../components/Event/EditRepeatingEventDialog";

const Show = ({ event, rsvpCount, voicePartsRsvpCount, attendanceCount, voicePartsAttendanceCount, addToCalendarLinks }) => {
    const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
    const [editDialogIsOpen, setEditDialogIsOpen] = useState(false);

    const { props: pageProps } = usePage();

    return (
        <>
            <AppHead title={`${event.title} - Events`} />
            <PageHeader
                title={<>{event.title}{event.is_repeating && <Icon icon="repeat" className="ml-1.5" />}</>}
                meta={[
                    <Badge>{event.type.title}</Badge>,
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

            <DeleteEventDialog isOpen={deleteDialogIsOpen} setIsOpen={setDeleteDialogIsOpen} event={event} />
            <EditRepeatingEventDialog isOpen={editDialogIsOpen} setIsOpen={setEditDialogIsOpen} event={event} />

            <div className="bg-gray-50 flex-grow">
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 h-full sm:divide-x sm:divide-x-gray-300">

                    <div className="sm:col-span-1 xl:col-span-3 flex flex-col justify-stretch divide-y divide-y-gray-300">

                        <div className="py-6 px-4 sm:px-6 lg:px-8">
                            <SectionHeader>
                                <SectionTitle>Event Location</SectionTitle>
                            </SectionHeader>
                            <p><strong>{event.location_name}</strong></p>
                            <p className="mb-8">{event.location_address}</p>

                            <GoogleMap placeId={event.location_place_id} />
                        </div>

                        <div className="py-6 px-4 sm:px-6 lg:px-8">
                            <SectionHeader>
                                <SectionTitle>Event Description</SectionTitle>
                            </SectionHeader>
                            <div className="mb-8">
                                {event.description}
                            </div>

                            <p className="text-sm text-gray-500 my-2">Choir's Timezone: {pageProps.tenant.timezone_label}</p>
                        </div>

                    </div>

                    <div className="sm:col-span-1 sm:divide-y sm:divide-y-gray-300">

                        <MyAttendance event={event} addToCalendarLinks={addToCalendarLinks} />

                        {event.can['update_event'] && <>
                            <RsvpSummary event={event} rsvpCount={rsvpCount} voicePartsRsvpCount={voicePartsRsvpCount} />
                            <AttendanceSummary event={event} attendanceCount={attendanceCount} voicePartsAttendanceCount={voicePartsAttendanceCount}/>
                        </>}

                    </div>

                </div>
            </div>
        </>
    );
}

Show.layout = page => <Layout children={page} />

export default Show;

const DeleteEventDialog = ({ isOpen, setIsOpen, event }) => (
    <Dialog
        title="Delete Event"
        okLabel="Delete"
        okUrl={route('events.destroy', event)}
        okVariant="danger-solid"
        okMethod="delete"
        isOpen={isOpen}
        setIsOpen={setIsOpen}
    >
        <p>
            Are you sure you want to delete this event? This action cannot be undone.
        </p>
    </Dialog>
);