import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import EventType from "../../EventType";
import collect from "collect.js";
import TableHeadingSort from "../../components/TableHeadingSort";
import Icon from "../../components/Icon";

const EventTableDesktop = ({ events, sortFilterForm }) => {
    const headings = collect({
        title: <TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>,
        type: <TableHeadingSort form={sortFilterForm} sort="type-title">Type</TableHeadingSort>,
        start_date: <TableHeadingSort form={sortFilterForm} sort="start_date">Event Date</TableHeadingSort>,
        location: 'Location',
        attendance: 'Attendance',
        created_at: <TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort>,
    });

    return (
        <Table
            headings={headings}
            body={events.map((event) => (
                <tr key={event.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="ml-4">
                                <Link href={route('events.show', event.id)} className="text-sm font-medium text-purple-800">
                                    {event.title}
                                    {event.is_repeating && <Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />}
                                </Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <Badge colour={(new EventType(event.type.title)).badgeColour}>{event.type.title}</Badge>
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
}

export default EventTableDesktop;