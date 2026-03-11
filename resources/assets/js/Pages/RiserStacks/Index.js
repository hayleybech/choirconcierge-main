import React from 'react'
import TenantLayout from "../../Layouts/TenantLayout";
import PageHeader from "../../components/PageHeader/PageHeader";
import AppHead from "../../components/AppHead";
import RiserStackTableDesktop from "./RiserStackTableDesktop";
import RiserStackTableMobile from "./RiserStackTableMobile";
import {usePage} from "@inertiajs/react";
import IndexContainer from "../../components/IndexContainer";
import EmptyState from "../../components/EmptyState";
import useRoute from "../../hooks/useRoute";
import useFilterPane from "../../hooks/useFilterPane";
import useSortFilterForm from "../../hooks/useSortFilterForm";
import FilterSortPane from "../../components/FilterSortPane";
import RiserStackFilters from "../../components/RiserStack/RiserStackFilters";

const Index = ({ stacks, ensembles, userEnsemblesCount }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const { can } = usePage().props;
    const { route } = useRoute();

    const filters = [
        { name: 'ensembles.id', multiple: true },
    ];

    const sortFilterForm = useSortFilterForm('stacks.index', filters, []);

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
                    filterAction,
                ].filter(action => action.can ? can[action.can] : true)}
                optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary' }
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        filters={<RiserStackFilters
                            ensembles={ensembles}
                            userEnsemblesCount={userEnsemblesCount}
                            form={sortFilterForm}
                        />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableDesktop={<RiserStackTableDesktop stacks={stacks} userEnsemblesCount={userEnsemblesCount} />}
                tableMobile={<RiserStackTableMobile stacks={stacks} userEnsemblesCount={userEnsemblesCount} />}
                emptyState={stacks.data.length === 0
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