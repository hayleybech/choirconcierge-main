import React from 'react'
import AppHead from "../../components/AppHead";
import PageHeader from "../../components/PageHeader";
import TenantLayout from "../../Layouts/TenantLayout";
import TaskForm from "./TaskForm";
import useRoute from "../../hooks/useRoute";

const Create = ({ roles }) => {
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Create Task" />
            <PageHeader
                title="Create Task"
                icon="tasks"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Tasks', url: route('tasks.index')},
                    { name: 'Create', url: route('tasks.create') },
                ]}
            />

            <TaskForm roles={roles} />
        </>
    );
}

Create.layout = page => <TenantLayout children={page} />

export default Create;