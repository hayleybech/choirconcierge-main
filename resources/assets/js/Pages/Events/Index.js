import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventTableDesktop from "./EventTableDesktop";
import EventTableMobile from "./EventTableMobile";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ events }) => {
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
                    { label: 'Filter', icon: 'filter', url: '#' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <div className="hidden lg:flex flex-col">
                <EventTableDesktop events={events} />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <EventTableMobile events={events} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;