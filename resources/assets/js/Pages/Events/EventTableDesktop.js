import React from 'react';
import {Link} from "@inertiajs/react";
import {DateTime} from "luxon";
import Table, {
    TableCell,
    THead,
    TBody,
    TableHeading,
    TableSelectAll,
    TableCellSelect, TItemRow,
} from "../../components/Table";
import Badge from "../../components/Badge";
import DateTag from "../../components/DateTag";
import EventType from "../../EventType";
import TableHeadingSort from "../../components/TableHeadingSort";
import Icon from "../../components/Icon";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';
import RsvpDropdown from '../../components/Event/RsvpDropdown';

const EventTableDesktop = ({ events, sortFilterForm, userEnsemblesCount, bulkEdit }) => {
    const { route } = useRoute();

    const showEnsemblesColumn = userEnsemblesCount > 1;

    return (
        <Table pagination={<Pagination details={events} />}>
            <THead>
                <tr>
                    <TableSelectAll bulkEdit={bulkEdit} totalItems={events.data.length} />
                    <TableHeading>
                        <TableHeadingSort form={sortFilterForm} sort="title">Title</TableHeadingSort>
                    </TableHeading>
                    <TableHeading>
                        <TableHeadingSort form={sortFilterForm} sort="type-title">Type</TableHeadingSort>
                    </TableHeading>
                    {showEnsemblesColumn && <TableHeading>Ensembles</TableHeading>}
                    <TableHeading>
                        <TableHeadingSort form={sortFilterForm} sort="start_date">Event Date</TableHeadingSort>
                    </TableHeading>
                    <TableHeading>Location</TableHeading>
                    <TableHeading>Attendance</TableHeading>
                    <TableHeading>
                        <TableHeadingSort form={sortFilterForm} sort="created_at">Date Created</TableHeadingSort>
                    </TableHeading>
                </tr>
            </THead>
            <TBody>
                {events.data.map((event) => (
                    <TItemRow key={event.id} bulkEdit={bulkEdit} value={event.id}>
                        <TableCellSelect bulkEdit={bulkEdit} value={event.id} />
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
                    </TItemRow>
                ))}
            </TBody>
        </Table>
    );
}

export default EventTableDesktop;