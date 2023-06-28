import React from 'react';
import Panel from "../../../components/Panel";
import SectionTitle from "../../../components/SectionTitle";
import TableMobile, { TableMobileItem, TableMobileLink, TableMobileListItem } from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";
import {DateTime} from "luxon";
import GoogleMap from "../../../components/GoogleMap";
import ButtonLink from "../../../components/inputs/ButtonLink";
import Icon from "../../../components/Icon";
import {usePage} from "@inertiajs/inertia-react";
import useRoute from "../../../hooks/useRoute";
import RsvpDropdown from "../../../components/Event/RsvpDropdown";

const CentralUpcomingEventsWidget = ({ events }) => {
    const { route } = useRoute();
    const { can } = usePage().props;

    return (
        <Panel header={<SectionTitle>Upcoming Events</SectionTitle>} noPadding>
            {events.length > 0 ? (
            <TableMobile>
                {events.map((event) => (
                    <TableMobileListItem key={event.id}>
                        {isToday(event) && (
                            <div className="flex items-center justify-between mt-4 -mb-2 px-4 sm:px-6 z-10">
                                <div className="text-md font-bold mr-2">Today</div>

                                {can['create_attendance'] && (
                                    <ButtonLink
                                        href={route('events.attendances.index', {tenant: event.tenant_id, event})}
                                        variant="primary"
                                        size="xs"
                                        className="mt-2"
                                    >
                                        <Icon icon="edit" />
                                        Record Attendance
                                    </ButtonLink>
                                )}
                            </div>
                        )}

                        <TableMobileLink url={route('events.show', {tenant: event.tenant_id, event})} key={event.id}>
                            <div className="flex-1 flex flex-col mr-2 sm:mr-4">
                                <div className="flex items-center justify-between gap-1">
                                    <div>
                                        <div className="text-sm font-medium text-purple-800">{event.title}</div>
                                        <div className="text-xs text-gray-500">{event.tenant.choir_name}</div>
                                    </div>
                                    <div className="text-sm hidden xl:block shrink-0">
                                        <DateTag date={event.call_time} format={isToday(event) ? 'TIME_24_SIMPLE' : 'DATE_MED'} />
                                    </div>
                                    <div className="text-sm xl:hidden shrink-0">
                                        <DateTag date={event.call_time} format={isToday(event) ? 'TIME_24_SIMPLE' : 'DATE_SHORT'} />
                                    </div>
                                </div>
                                {isToday(event) && (
                                    <div className="mt-2">
                                        <p className="text-sm text-gray-500 font-bold">{event.location_name}</p>
                                        <p className="text-sm text-gray-500">{event.location_address}</p>
                                        {event.location_place_id && <GoogleMap placeId={event.location_place_id} />}
                                    </div>
                                )}
                            </div>
                        </TableMobileLink>

                        {! isToday(event) && (
                          <div className="-mt-2 mb-4 self-stretch md:self-start px-4 sm:px-6 z-10">
                              <RsvpDropdown event={event} size="xs" />
                          </div>
                        )}
                    </TableMobileListItem>
                ))}
            </TableMobile>
            ) : (
                <p className="px-4 py-4 sm:px-6">No events this month.</p>
            )}
        </Panel>
    );
}

export default CentralUpcomingEventsWidget;

function isToday(event) {
    return DateTime.fromISO(event.call_time).hasSame(DateTime.now(), 'day');
}