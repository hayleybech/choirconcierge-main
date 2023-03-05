import React from 'react'
import TenantLayout from "../../../Layouts/TenantLayout";
import PageHeader from "../../../components/PageHeader";
import AppHead from "../../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import Calendar from "./../Calendar";

const Month = ({ days, month }) => {
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
                    { label: 'List View', icon: 'th-list', url: route('events.index'), can: 'list_events' },
                ].filter(action => action.can ? can[action.can] : true)}
                meta={[<div className="text-gray-400">Calendar Sync URL: {route('events.feed')}</div>]}
            />

            <Calendar days={days} month={month} />
        </>
    );
}

Month.layout = page => <TenantLayout children={page} />

export default Month;