import React from 'react'
import Layout from "../../../Layouts/Layout";
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import useFilterPane from "../../../hooks/useFilterPane";
import Calendar from "./../Calendar";

const Index = ({ days, month, eventTypes }) => {
    const [showFilters, setShowFilters] = useFilterPane();
    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Calendar - Month View" />
            <PageHeader
                title="Calendar"
                icon="calendar-alt"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Events', url: route('events.index')},
                    { name: 'Calendar', url: route('events.calendar.month')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'calendar-plus', url: route('events.create'), variant: 'primary', can: 'create_event' },
                    { label: 'Attendance Report', icon: 'analytics', url: route('events.reports.attendance'), can: 'list_attendances' },
                    { label: 'Filter/Sort', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                    { label: 'List View', icon: 'th-list', url: route('events.index'), can: 'list_events' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <Calendar days={days} month={month} />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;