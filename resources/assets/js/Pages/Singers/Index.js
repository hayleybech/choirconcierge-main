import React from 'react'
import Layout from "../../Layouts/Layout";
import PageHeader from "../../components/PageHeader";
import SingerTableDesktop from "./SingerTableDesktop";
import SingerTableMobile from "./SingerTableMobile";
import AppHead from "../../components/AppHead";

const Index = ({all_singers}) => (
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
                { label: 'Add New', icon: 'user-plus', url: route('singers.create'), variant: 'primary'},
                { label: 'Voice Parts', icon: 'users-class', url: route('voice-parts.index')},
                { label: 'Filter', icon: 'filter', url: '#'},
            ]}
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

Index.layout = page => <Layout children={page} />

export default Index;