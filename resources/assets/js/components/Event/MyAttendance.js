import React from 'react';
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";
import Button from "../inputs/Button";
import Icon from "../Icon";
import AddToCalendarDropdown from "./AddToCalendarDropdown";
import RsvpTag from "./RsvpTag";

const MyAttendance = ({ event, addToCalendarLinks }) => (
    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <SectionHeader>
            <SectionTitle>My Attendance</SectionTitle>
        </SectionHeader>

        <h3 className="text-lg mt-3 mb-1">RSVP</h3>
        <p>
            <span className="mr-2">Your response:</span>
            <RsvpTag label={event.my_rsvp.label} icon={event.my_rsvp.icon} colour={event.my_rsvp.colour} />
        </p>

        {event.in_future && (<>
            <div className="my-2">

                {event.my_rsvp?.response !== 'yes' && (
                    <Button
                        href={event.my_rsvp.id ? route('events.rsvps.update', [event, event.my_rsvp]) : route('events.rsvps.store', event)}
                        method={event.my_rsvp.id ? 'put' : 'post'}
                        size="sm"
                        variant="success-outline"
                        className="mr-2"
                        data={{rsvp_response: 'yes'}}
                    >
                        <Icon icon="check" mr/>
                        I'm Going
                    </Button>
                )}
                {event.my_rsvp?.response !== 'no' && (
                    <Button
                        href={event.my_rsvp.id ? route('events.rsvps.update', [event, event.my_rsvp]) : route('events.rsvps.store', event)}
                        method={event.my_rsvp.id ? 'put' : 'post'}
                        size="sm"
                        variant="danger-outline"
                        data={{rsvp_response: 'no'}}
                    >
                        <Icon icon="times" mr/>
                        I'm Not Going
                    </Button>
                )}
            </div>

            <div className="mt-6">
                <AddToCalendarDropdown urls={addToCalendarLinks} />
            </div>
        </>)}

        {! event.in_future &&
            <>
                <h3 className="text-lg mt-3 mb-1">Attendance</h3>
                <p>
                    <span className="mr-2">You were:</span>
                    <RsvpTag label={event.my_attendance.label} icon={event.my_attendance.icon} colour={event.my_attendance.colour} />
                </p>
            </>
        }
    </div>
);

export default MyAttendance;