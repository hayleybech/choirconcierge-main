import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import TaskTableDesktop from "./TaskTableDesktop";
import TaskTableMobile from "./TaskTableMobile";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ tasks }) => {
    const { can } = usePage().props;

    return (
        <>
            <AppHead title="Onboarding" />
            <PageHeader
                title="Onboarding Tasks"
                icon="tasks"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Tasks', url: route('tasks.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('tasks.create'), variant: 'primary', can: 'create_task' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <div className="hidden lg:flex flex-col">
                <TaskTableDesktop tasks={tasks} />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <TaskTableMobile tasks={tasks} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;