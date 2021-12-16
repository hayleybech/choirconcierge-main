import React, {useState} from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";
import IndexContainer from "../../components/IndexContainer";
import SingerFilters from "../../components/SingerFilters";

const Index = ({ allSingers, statuses, defaultStatus, voiceParts, roles }) => {
    const [showFilters, setShowFilters] = useState(false);
    const { can } = usePage().props;

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
                    { label: 'Filter', icon: 'filter', onClick: () => setShowFilters(! showFilters) },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            <IndexContainer
                showFilters={showFilters}
                filters={<SingerFilters statuses={statuses} defaultStatus={defaultStatus} voiceParts={voiceParts} roles={roles} onClose={() => setShowFilters(false)} />}
                tableMobile={<SingerTableMobile singers={allSingers} />}
                tableDesktop={<SingerTableDesktop singers={allSingers} />}
            />
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;