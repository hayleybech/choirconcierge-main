import React from 'react'
import AppHead from "../../components/AppHead";
import PageHeader from "../../components/PageHeader";
import Layout from "../../Layouts/Layout";
import TaskForm from "./TaskForm";

const Create = ({ roles }) => (
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

Create.layout = page => <Layout children={page} />

export default Create;