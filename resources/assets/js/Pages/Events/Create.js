import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventForm from "./EventForm";

const Create = ({ types }) => (
    <>
        <AppHead title="Create Event" />
        <PageHeader
            title="Create Event"
            icon="calendar-plus"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Events', url: route('events.index')},
                { name: 'Create', url: route('events.create')},
            ]}
        />

        <EventForm types={types} />
    </>
);

Create.layout = page => <Layout children={page} />

export default Create;