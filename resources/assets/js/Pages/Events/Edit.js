import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventForm from "./EventForm";

const Edit = ({ event, types, mode }) => (
    <>
        <AppHead title={`Edit - ${event.title}`} />
        <PageHeader
            title="Edit Event"
            icon="calendar-edit"
            breadcrumbs={[
                { name: 'Dashboard', url: route('dash')},
                { name: 'Events', url: route('events.index')},
                { name: event.title, url: route('events.show', event)},
                { name: 'Edit', url: route('events.edit', event)},
            ]}
        />

        <EventForm event={event} types={types} mode={mode} />
    </>
);

Edit.layout = page => <TenantLayout children={page} />

export default Edit;