import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Badge from "../../components/Badge";
import {DateTime} from "luxon";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import EventType from "../../EventType";
import useRoute from "../../hooks/useRoute";

const EventTableMobile = ({ events }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {events.map((event) => (
                <TableMobileItem key={event.id} url={route('events.show', {event})}>
                    <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div className="flex items-center justify-between">
                            <p className="flex items-center min-w-0 mr-1.5">
                            <span className="text-sm font-medium text-purple-600 truncate">
                                {event.title}
                                {event.is_repeating && <Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />}
                            </span>
                            </p>
                            <div className="text-xs text-gray-500">
                                {DateTime.fromJSDate(new Date(event.call_time)) < DateTime.now()
                                    ? <p>{event.present_count}&nbsp;present</p>
                                    : <p>{event.going_count}&nbsp;going</p>
                                }
                            </div>
                        </div>
                        <div className="flex items-center justify-between">
                            <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                                <DateTag date={event.call_time} format="DATETIME_MED_WITH_WEEKDAY" />
                            </p>

                            <p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
                                <Badge colour={(new EventType(event.type.title)).badgeColour}>{event.type.title}</Badge>
                            </p>
                        </div>
                        {event.location_name &&(
                            <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                                <Icon icon="map-marker-alt" mr className="text-gray-400" />
                                <span className="truncate">{event.location_name}</span>
                            </p>
                        )}
                    </div>
                </TableMobileItem>
            ))}
        </TableMobile>
    );
}

export default EventTableMobile;