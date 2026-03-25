import React from 'react';
import {Link} from "@inertiajs/react";
import {DateTime} from "luxon";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import EventType from "../../EventType";
import collect from "collect.js";
import TableHeadingSort from "../../components/TableHeadingSort";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import RsvpDropdown from '../../components/Event/RsvpDropdown';

const EventTableDesktop = ({ events, sortFilterForm, pagination, userEnsemblesCount }) => {
    const { route } = useRoute();

    const showEnsemblesColumn = userEnsemblesCount > 1;

    const headings = collect({
        title: (
            <TableHeading>
                <TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>
            </TableHeading>
        ),
        type: (
            <TableHeading>
                <TableHeadingSort form={sortFilterForm} sort="type-title">Type</TableHeadingSort>
            </TableHeading>
        ),
        ensembles: showEnsemblesColumn ? <TableHeading>Ensembles</TableHeading> : null,
        start_date: (
            <TableHeading>
                <TableHeadingSort form={sortFilterForm} sort="start_date">Event Date</TableHeadingSort>
            </TableHeading>
        ),
        location: <TableHeading>Location</TableHeading>,
        attendance: <TableHeading>Attendance</TableHeading>,
        created_at: (
            <TableHeading>
                <TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort>
            </TableHeading>
        ),
    }).filter(heading => heading !== null);

    return (
        <Table pagination={<Pagination details={pagination} />}>
            <THead>
                <tr>{headings.values().toArray()}</tr>
            </THead>
            <TBody>
                {events.map((event) => (
                    <tr key={event.id}>
                        <TableCell>
                            <Link href={route('events.show', {event})} className="text-sm font-medium text-purple-800">
                                {event.title}
                                {event.is_repeating && <Icon icon={event.is_repeat_parent ? 'repeat-1' : 'repeat'} className="ml-1.5" />}
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Badge colour={(new EventType(event.type.title)).badgeColour}>{event.type.title}</Badge>
                        </TableCell>
                        {showEnsemblesColumn && (
                            <TableCell>
                                <div className="space-x-1.5 space-y-1.5">
                                    {event.ensembles.map(ensemble => (
                                        <Badge key={ensemble.id} colour="bg-purple-100 text-purple-800">{ensemble.name}</Badge>
                                    ))}
                                </div>
                            </TableCell>
                        )}
                        <TableCell>
                            <DateTag date={event.call_time} />
                        </TableCell>
                        <TableCell>
                            {event.location_name}
                        </TableCell>
                        <TableCell>
                            <div className="flex gap-2 items-center">
                                <div>
                                    {DateTime.fromJSDate(new Date(event.call_time)) < DateTime.now()
                                        ? <p>{event.present_count}&nbsp;present</p>
                                        : <p>{event.going_count}&nbsp;going</p>
                                    }
                                </div>
                                {DateTime.fromJSDate(new Date(event.call_time)) > DateTime.now() && (
                                    <RsvpDropdown event={event} size="xs" />
                                )}
                            </div>
                        </TableCell>
                        <TableCell>
                            <DateTag date={event.created_at} />
                        </TableCell>
                    </tr>
                ))}
            </TBody>
        </Table>
    );
}

export default EventTableDesktop;