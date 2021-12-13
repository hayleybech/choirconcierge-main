import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventTableDesktop from "./EventTableDesktop";
import EventTableMobile from "./EventTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import EventFilters from "../../components/Event/EventFilters";

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

            <div className="flex flex-col lg:flex-row">
                {showFilters && (
                <div className="lg:w-1/5 xl:w-1/6 border-b lg:border-r border-gray-300 lg:z-10">
                    <EventFilters eventTypes={eventTypes} onClose={() => setShowFilters(false)} />
                </div>
                )}

                <div className="flex-grow lg:overflow-x-auto">
                    <div className="hidden lg:flex flex-col overflow-y-hidden">
                        <EventTableDesktop events={events} />
                    </div>

                    <div className="bg-white shadow block lg:hidden">
                        <EventTableMobile events={events} />
                    </div>
                </div>
            </div>

        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;