import React from 'react';
import AppHead from "../../../components/AppHead";
import PageHeader from "../../../components/PageHeader";
import route from 'ziggy-js';
import { Ziggy } from '../../../../../js/ziggy';
import Layout from "../../../Layouts/Layout";
import IndexContainer from "../../../components/IndexContainer";
import ActivityTableMobile from "../Attendance/ActivityTableMobile";
import ActivityTableDesktop from "../Attendance/ActivityTableDesktop";

const Index = ({ event }) => (
    <>
        <AppHead title={`Schedule - ${event.title} - Events`} />
        <PageHeader
            title="Event Schedule"
            icon="calendar-alt"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash', undefined, undefined, Ziggy)},
                { name: 'Events', url: route('events.index', undefined, undefined, Ziggy)},
                { name: event.title, url: route('events.show', event, undefined, Ziggy) },
                { name: 'Schedule', url: route('events.activities.index', event, undefined, Ziggy)}
            ]}
        />

        <IndexContainer
            tableMobile={<ActivityTableMobile activities={[]} />}
            tableDesktop={<ActivityTableDesktop activities={[]} />}
        />

    </>
);

Index.layout = page => <Layout children={page} />

export default Index;