import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventTableDesktop from "./EventTableDesktop";
import EventTableMobile from "./EventTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import EventFilters from "../../components/Event/EventFilters";
import IndexContainer from "../../components/IndexContainer";
import FilterSortPane from "../../components/FilterSortPane";
import useFilterPane from "../../hooks/useFilterPane";
import Sorts from "../../components/Sorts";
import useSortFilterForm from "../../hooks/useSortFilterForm";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";

const Index = ({ events, eventTypes }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const { can } = usePage().props;
    const { route } = useRoute();

    const sorts = [
        { id: 'title', name: 'Title' },
        { id: 'start_date', name: 'Event Date', default: true },
        { id: 'type-title', name: 'Type' },
        { id: 'created_at', name: 'Date Created' },
    ];

    const filters = [
        { name: 'title', defaultValue: '' },
        { name: 'type.id', multiple: true },
        { name: 'date', defaultValue: 'upcoming' },
    ];

    const transforms = (data) => ({
        date: data.date === 'all' ? null : data.date,
    });

    const sortFilterForm = useSortFilterForm('events.index', filters, sorts, transforms);

    return (
        <>
            <AppHead title="Events" />
            <PageHeader
                title="Events"
                icon="calendar"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Events', url: route('events.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'calendar-plus', url: route('events.create'), variant: 'primary', can: 'create_event' },
                    { label: 'Event Types', icon: 'tags', url: route('event-types.index'), can: 'list_events' },
                    { label: 'Attendance Report', icon: 'analytics', url: route('events.reports.attendance'), can: 'list_attendances' },
                    filterAction,
                    { label: 'Calendar View', icon: 'calendar-alt', url: route('events.calendar.month') },
                ].filter(action => action.can ? can[action.can] : true)}
                meta={[<div className="text-gray-400">Calendar Sync URL: {route('events.feed')}</div>]}
                optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary' }
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
                        filters={<EventFilters eventTypes={eventTypes} form={sortFilterForm} />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableMobile={<EventTableMobile events={events} />}
                tableDesktop={<EventTableDesktop events={events} sortFilterForm={sortFilterForm} />}
                emptyState={events.length === 0
                    ? <EmptyState
                        title="No events"
                        actionDescription={can['create_event']
                            ? "Get started by adding an event like a rehearsal or performance. Otherwise, try expanding your filtering options."
                            : "Your team might not have added any events yet. Otherwise, you may need to expand your filtering options."
                        }
                        icon="calendar"
                        href={can['create_event'] ? route('events.create') : null}
                        actionLabel="Add Event"
                        actionIcon="calendar-plus"
                    />
                    : null
                }
            />
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;