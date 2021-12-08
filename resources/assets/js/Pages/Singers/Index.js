import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";
import AppHead from "../../components/AppHead";
import {usePage} from "@inertiajs/inertia-react";

const Index = ({ all_singers }) => {
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
                    { label: 'Filter', icon: 'filter', url: '#' },
                ].filter(action => action.can ? can[action.can] : true)}
            />

            {/* Desktop Table */}
            <div className="hidden lg:flex flex-col">
                <SingerTableDesktop singers={all_singers} />
            </div>

            {/* Mobile Table */}
            <div className="bg-white shadow block lg:hidden">
                <SingerTableMobile singers={all_singers} />
            </div>
        </>
    );
}

Index.layout = page => <Layout children={page} />

export default Index;