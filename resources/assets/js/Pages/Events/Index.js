import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventTableDesktop from "./EventTableDesktop";
import EventTableMobile from "./EventTableMobile";

const Index = ({ events }) => (
    <>
        <AppHead title="Events" />
        <PageHeader
            title="Events"
            icon="fa-calendar-alt"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Events', url: route('events.index')},
            ]}
            actions={[
                { label: 'Add New', icon: 'plus', url: route('events.create'), variant: 'primary'},
                { label: 'Filter', icon: 'filter', url: '#'},
            ]}
        />

        <div className="hidden lg:flex flex-col">
            <EventTableDesktop events={events} />
        </div>

        <div className="bg-white shadow overflow-hidden block lg:hidden">
            <EventTableMobile events={events} />
        </div>
    </>
);

Index.layout = page => <Layout children={page} />

export default Index;