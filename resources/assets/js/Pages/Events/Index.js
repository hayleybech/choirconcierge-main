import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventTableDesktop from "./EventTableDesktop";
import EventTableMobile from "./EventTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import EventFilters from "../../components/Event/EventFilters";
import IndexContainer from "../../components/IndexContainer";

const Index = ({ events, eventTypes }) => {
    const [showFilters, setShowFilters] = useState(false);
    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Events" />
            <PageHeader
                title="Events"
                icon="calendar-alt"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Events', url: route('events.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'calendar-plus', url: route('events.create'), variant: 'primary', can: 'create_event' },
                    { label: 'Attendance Report', icon: 'analytics', url: route('events.reports.attendance'), can: 'list_attendances' },
                    { label: 'Filter', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                showFilters={showFilters}
                filters={<EventFilters eventTypes={eventTypes} onClose={() => setShowFilters(false)} />}
                tableMobile={<EventTableMobile events={events} />}
                tableDesktop={<EventTableDesktop events={events} />}
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;