import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackTableDesktop from "./RiserStackTableDesktop";
import RiserStackTableMobile from "./RiserStackTableMobile";
import {usePage} from "@inertiajs/react";
import IndexContainer from "../../components/IndexContainer";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";

const Index = ({ stacks }) => {
    const { can } = usePage().props;
    const { route } = useRoute();

    return (
        <>
            <AppHead title="Riser Stacks" />
            <PageHeader
                title="Riser Stacks"
                icon="people-arrows"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Riser Stacks', url: route('stacks.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'plus', url: route('stacks.create'), variant: 'primary', can: 'create_stack' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                tableDesktop={<RiserStackTableDesktop stacks={stacks} />}
                tableMobile={<RiserStackTableMobile stacks={stacks} />}
                emptyState={stacks.length === 0
                    ? <EmptyState
                        title="No riser stacks"
                        description="Riser stacks allow you to track where your singers should be physically positioned. "
                        actionDescription={can['create_stack']
                            ? "You haven't made any yet. Press the button and get started!"
                            : "Your team haven't made any yet."
                        }
                        icon="people-arrows"
                        href={can['create_stack'] ? route('stacks.create') : null}
                        actionLabel="Add Riser Stack"
                        actionIcon="plus"
                    />
                    : null
                }
            />
        </>
    );
}

Index.layout = page => <TenantLayout children={page} />

export default Index;