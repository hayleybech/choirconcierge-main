import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import IndexContainer from "../../components/IndexContainer";
import SingerFilters from "../../components/SingerFilters";
import useFilterPane from "../../hooks/useFilterPane";
import FilterSortPane from "../../components/FilterSortPane";
import Sorts from "../../components/Sorts";
import useSortFilterForm from "../../hooks/useSortFilterForm";

const Index = ({ allSingers, statuses, defaultStatus, voiceParts, roles }) => {
    const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
    const { can } = usePage().props;

    const sorts = [
        { id: 'full-name', name: 'Name', default: true },
        { id: 'status-title', name: 'Status' },
        { id: 'part-title', name: 'Voice Part' },
        { id: 'paid_until', name: 'Paid Until' },
    ];

    const filters = [
        { name: 'user.name', defaultValue: '' },
        { name: 'category.id', multiple: true, defaultValue: [defaultStatus] },
        { name: 'voice_part.id', multiple: true },
        { name: 'roles.id', multiple: true },
        { name: 'fee_status', defaultValue: '' },
    ];

    const sortFilterForm = useSortFilterForm('singers.index', filters, sorts);

    return (
        <>
            <AppHead title="Singers" />
            <PageHeader
                title="Singers"
                icon="users"
                breadcrumbs={[
                    { name: 'Dashboard', url: route('dash')},
                    { name: 'Singers', url: route('singers.index')},
                ]}
                actions={[
                    { label: 'Add New', icon: 'user-plus', url: route('singers.create'), variant: 'primary', can: 'create_singer' },
                    { label: 'Voice Parts', icon: 'users-class', url: route('voice-parts.index'), can: 'list_voice_parts' },
                    { label: 'User Roles', icon: 'user-tag', url: route('roles.index'), can: 'list_roles' },
                    filterAction,
                ].filter(action => action.can ? can[action.can] : true)}
                optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary' }
            />

            <IndexContainer
                showFilters={showFilters}
                filterPane={
                    <FilterSortPane
                        sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
                        filters={<SingerFilters statuses={statuses} voiceParts={voiceParts} roles={roles} form={sortFilterForm} />}
                        closeFn={() => setShowFilters(false)}
                    />
                }
                tableMobile={<SingerTableMobile singers={allSingers} />}
                tableDesktop={<SingerTableDesktop singers={allSingers} sortFilterForm={sortFilterForm} />}
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;