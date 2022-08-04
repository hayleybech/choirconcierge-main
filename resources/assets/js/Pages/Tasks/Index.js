import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import TaskTableDesktop from "./TaskTableDesktop";
import TaskTableMobile from "./TaskTableMobile";
import {usePage} from "@inertiajs/inertia-react";
import EmptyState from "../../components/EmptyState";
import IndexContainer from "../../components/IndexContainer";

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

            <IndexContainer
                tableDesktop={<TaskTableDesktop tasks={tasks} />}
                tableMobile={<TaskTableMobile tasks={tasks} />}
                emptyState={tasks.length === 0
                    ? <EmptyState
                        title="No onboarding tasks"
                        description={<>
                            Onboarding tasks allow you to keep track of your recruitment process and send reminders and resources to prospects and team members. <br />
                            Looks like there are no onboarding tasks set up yet.
                        </>}
                        actionDescription={can['create_task'] ? 'Create a task like "Pass Audition", then create some notifications e.g. "Congratulations".' : null}
                        icon="tasks"
                        href={can['create_task'] ? route('tasks.create') : null}
                        actionLabel="Add Task"
                        actionIcon="plus"
                    />
                    : null
                }
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;