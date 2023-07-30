import React from 'react';
import AddToCalendarDropdown from "./AddToCalendarDropdown";
import RsvpTag from "./RsvpTag";
import CollapsePanel from "../CollapsePanel";
import RsvpDropdown from "./RsvpDropdown";

const MyAttendance = ({ event, addToCalendarLinks }) => (
    <CollapsePanel>
        <h3 className="text-lg mt-3 mb-1">RSVP</h3>

      {! event.in_future &&
        <p className="mb-2">
            <RsvpTag label={event.my_rsvp.label} icon={event.my_rsvp.icon} colour={event.my_rsvp.colour} />
        </p>
      }

        {event.in_future && (<>
          <div className="mt-2 self-stretch md:self-end">
            <RsvpDropdown event={event} />
          </div>

          <div className="mt-6 self-stretch md:self-start">
            <AddToCalendarDropdown urls={addToCalendarLinks} size="xs" />
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
    </CollapsePanel>
);

export default MyAttendance;