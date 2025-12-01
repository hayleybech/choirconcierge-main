import React from 'react';
import {useMediaQuery} from "react-responsive";
import EventScheduleDesktop from "./EventScheduleDesktop";
import EventScheduleMobile from "./EventScheduleMobile";
import EmptyState from "../EmptyState";
import ScheduleItemForm from "../ScheduleItemForm";

const EventSchedule = ({ event }) => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    return (
        <div>
            {event.activities.length > 0
                ? (isDesktop ? <EventScheduleDesktop event={event} /> : <EventScheduleMobile event={event} />)
                : <EmptyState
                    title="Empty schedule"
                    description={<>
                        Looks like this event doesn't have a schedule. This tool is great for rehearsals and performances. <br />
                        Schedules can assign songs and estimate the event duration.
                    </>}
                    actionDescription={event.can['update_event'] ? 'To get started, use the form below.' : null}
                    icon="stream"
                />
            }

            {event.can.update_event && (
                <div className="md:max-w-5xl md:mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                    <ScheduleItemForm event={event} />
                </div>
            )}
        </div>
    );
};

export default EventSchedule;