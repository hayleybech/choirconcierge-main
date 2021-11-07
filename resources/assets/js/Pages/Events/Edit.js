import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventForm from "./EventForm";

const Edit = ({ event, types }) => (
    <>
        <AppHead title={`Edit - ${event.title}`} />
        <PageHeader
            title="Edit Event"
            icon="calendar-alt"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Events', url: route('events.index')},
                { name: event.title, url: route('events.show', event)},
                { name: 'Edit', url: route('events.edit', event)},
            ]}
        />

        <EventForm event={event} types={types} />
    </>
);

Edit.layout = page => <Layout children={page} />

export default Edit;