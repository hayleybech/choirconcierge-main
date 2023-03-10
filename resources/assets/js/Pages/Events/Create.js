import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import EventForm from "./EventForm";
import useRoute from "../../hooks/useRoute";

const Create = ({ types }) => {
    const { route } = useRoute();

    return (
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
}

Create.layout = page => <TenantLayout children={page} />

export default Create;