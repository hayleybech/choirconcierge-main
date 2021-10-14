import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Badge from "../../components/Badge";
import {DateTime} from "luxon";

const EventTableMobile = ({ events }) => (
    <TableMobile>
        {events.map((event) => (
            <TableMobileItem key={event.id} url={route('events.show', event.id)}>
                <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                    <div className="flex items-center justify-between">
                        <p className="flex items-center min-w-0 mr-1.5">
                            <span className="text-sm font-medium text-indigo-600 truncate">{event.title}</span>
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
                            <i className="fas fa-fw fa-calendar-day mr-1.5" />
                            {DateTime.fromJSDate(new Date(event.call_time)).toLocaleString(DateTime.DATETIME_MED_WITH_WEEKDAY)}
                        </p>

                        <p className="mt-2 flex items-center text-sm text-gray-500 min-w-0">
                            <Badge>{event.type.title}</Badge>
                        </p>
                    </div>
                    {event.location_name &&(
                    <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                        <i className="fas fa-fw fa-map-marker-alt mr-1.5" />
                        <span className="truncate">{event.location_name}</span>
                    </p>
                    )}
                </div>
            </TableMobileItem>
        ))}
    </TableMobile>
);

export default EventTableMobile;