import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";

const EventTableDesktop = ({ events }) => (
    <Table
        headings={['Title', 'Type', 'Event Date', 'Location', 'Attendance', 'Created']}
        body={events.map((event) => (
            <tr key={event.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div className="ml-4">
                            <Link href={route('events.show', event.id)} className="text-sm font-medium text-purple-800">{event.title}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    <Badge>{event.type.title}</Badge>
                </TableCell>
                <TableCell>
                    <DateTag date={event.call_time} />
                </TableCell>
                <TableCell>
                    {event.location_name}
                </TableCell>
                <TableCell>
                    {DateTime.fromJSDate(new Date(event.call_time)) < DateTime.now()
                        ? <p>{event.present_count}&nbsp;present</p>
                        : <p>{event.going_count}&nbsp;going</p>
                    }
                </TableCell>
                <TableCell>
                    <DateTag date={event.created_at} />
                </TableCell>
            </tr>
        ))}
    />
);

export default EventTableDesktop;