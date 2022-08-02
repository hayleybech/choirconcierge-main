import React from 'react'
import Layout from "../../Layouts/Layout";
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

const Index = ({ events, eventTypes }) => {
    const [showFilters, setShowFilters] = useFilterPane();
    const { can } = usePage().props;

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
                    { label: 'Attendance Report', icon: 'analytics', url: route('events.reports.attendance'), can: 'list_attendances' },
                    { label: 'Filter/Sort', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                    { label: 'Calendar View', icon: 'calendar-alt', url: route('events.calendar.month') },
                ].filter(action => action.can ? can[action.can] : true)}
                meta={[<div className="text-gray-400">Calendar Sync URL: {route('events.feed')}</div>]}
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
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;