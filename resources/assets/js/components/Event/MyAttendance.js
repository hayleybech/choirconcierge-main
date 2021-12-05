import React from 'react';
import SectionTitle from "../SectionTitle";
import SectionHeader from "../SectionHeader";
import Button from "../inputs/Button";
import Icon from "../Icon";
import AddToCalendarDropdown from "./AddToCalendarDropdown";
import RsvpTag from "./RsvpTag";
import MyRsvpButtons from "./MyRsvpButtons";

const MyAttendance = ({ event, addToCalendarLinks }) => (
    <div className="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <SectionHeader>
            <SectionTitle>My Attendance</SectionTitle>
        </SectionHeader>

        <h3 className="text-lg mt-3 mb-1">RSVP</h3>
        <p className="mb-2">
            <span className="mr-2">Your response:</span>
            <RsvpTag label={event.my_rsvp.label} icon={event.my_rsvp.icon} colour={event.my_rsvp.colour} />
        </p>

        {event.in_future && (<>
            <MyRsvpButtons event={event} />

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